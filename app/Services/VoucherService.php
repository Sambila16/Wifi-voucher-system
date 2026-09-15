<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Single source of truth for "how a voucher comes into existence".
 *
 * RULE ENFORCED HERE: a Voucher row is only ever pushed to MikroTik
 * (status -> 'issued') from inside generateForPayment(), and that method
 * refuses to run unless the linked Payment is 'completed'. Nothing else
 * in the codebase should call MikrotikService::createHotspotUser()
 * directly — controllers go through this service.
 */
class VoucherService
{
    /**
     * Create a voucher for an already-completed payment (manual cash sale
     * or confirmed mobile money webhook) and provision it on the router.
     */
    public function generateForPayment(Payment $payment): Voucher
    {
        if ($payment->status !== 'completed') {
            throw new RuntimeException(
                "Refusing to generate voucher: payment #{$payment->id} is '{$payment->status}', not 'completed'."
            );
        }

        return DB::transaction(function () use ($payment) {
            $plan = $payment->plan;
            $router = Router::where('is_active', true)->firstOrFail();

            $voucher = Voucher::create([
                'code' => $this->generateUniqueCode(),
                'password' => $this->generateUniqueCode(6),
                'plan_id' => $plan->id,
                'router_id' => $router->id,
                'status' => 'pending',
                'source' => $payment->gateway === 'manual' ? 'manual' : 'automatic',
                'customer_phone' => $payment->customer_phone,
                'expires_at' => now()->addDays($plan->validity_days),
            ]);

            $payment->update(['voucher_id' => $voucher->id]);

            $mikrotik = new MikrotikService($router);
            $pushed = $mikrotik->createHotspotUser($voucher);

            if (! $pushed) {
                // Payment succeeded but router push failed (e.g. router offline).
                // Voucher stays 'pending' — a retry job / admin action can
                // re-push it. Customer already paid, so we never silently drop it.
                throw new RuntimeException(
                    "Payment #{$payment->id} completed but router provisioning failed for voucher {$voucher->code}. Voucher saved as 'pending' for retry."
                );
            }

            $voucher->update([
                'status' => 'issued',
                'issued_at' => now(),
            ]);

            return $voucher;
        });
    }

    /** Retry pushing a 'pending' voucher to its router (e.g. after router downtime). */
    public function retryIssue(Voucher $voucher): bool
    {
        $mikrotik = new MikrotikService($voucher->router);
        $pushed = $mikrotik->createHotspotUser($voucher);

        if ($pushed) {
            $voucher->update(['status' => 'issued', 'issued_at' => now()]);
        }

        return $pushed;
    }

    public function revoke(Voucher $voucher): void
    {
        $mikrotik = new MikrotikService($voucher->router);
        $mikrotik->removeHotspotUser($voucher->code);
        $mikrotik->kickActiveSession($voucher->code);

        $voucher->update(['status' => 'revoked', 'revoked_at' => now()]);
    }

    /** Called by the hotspot login walled-garden endpoint when a device first authenticates. */
    public function markActivated(Voucher $voucher, string $macAddress): void
    {
        if (! $voucher->isUsable()) {
            throw new RuntimeException("Voucher {$voucher->code} is not usable (status: {$voucher->status}).");
        }

        if ($voucher->status === 'issued') {
            $mikrotik = new MikrotikService($voucher->router);
            $mikrotik->bindMacAddress($voucher->code, $macAddress);

            $voucher->update([
                'status' => 'active',
                'activated_at' => now(),
                'mac_address' => $macAddress,
            ]);
        }
    }

    private function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }
}
