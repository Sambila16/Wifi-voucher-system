<?php

namespace App\Services;

use App\Models\Router;
use App\Models\Voucher;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Talks to a MikroTik router over the RouterOS API to manage hotspot users.
 *
 * This is the ONLY class that touches the router directly. Everything else
 * (VoucherService, controllers) goes through here, so security rules
 * (no user without payment) live in one place.
 */
class MikrotikService
{
    private Client $client;

    public function __construct(private Router $router)
    {
        $config = new Config([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->decrypted_password,
            'port' => $router->api_port,
            'ssl'  => $router->use_ssl,
        ]);

        $this->client = new Client($config);
    }

    /**
     * Push a voucher to the router as a hotspot user. Called ONLY after a
     * payment (manual or automatic) is confirmed as 'completed'. This is
     * the single point where "paying" turns into "actually gets internet".
     */
    public function createHotspotUser(Voucher $voucher): bool
    {
        $plan = $voucher->plan;

        $query = (new Query('/ip/hotspot/user/add'))
            ->equal('name', $voucher->code)
            ->equal('password', $voucher->password ?? $voucher->code)
            ->equal('profile', $plan->mikrotik_profile)
            ->equal('server', $voucher->router->hotspot_server)
            ->equal('limit-uptime', $this->formatUptime($plan->time_limit_minutes))
            ->equal('limit-bytes-total', $this->formatBytes($plan->data_limit_mb))
            ->equal('comment', 'voucher:' . $voucher->id . ':' . $voucher->source);

        $response = $this->client->query($query)->read();

        if (isset($response[0]['after']['ret'])) {
            Log::info("Mikrotik: created hotspot user {$voucher->code}");
            return true;
        }

        Log::error("Mikrotik: failed to create hotspot user {$voucher->code}", $response);
        return false;
    }

    /**
     * Remove/disable a voucher on the router — used for revoke or expiry.
     */
    public function removeHotspotUser(string $voucherCode): bool
    {
        $find = (new Query('/ip/hotspot/user/print'))
            ->where('name', $voucherCode);
        $result = $this->client->query($find)->read();

        if (empty($result)) {
            return true; // already gone
        }

        $id = $result[0]['.id'];
        $remove = (new Query('/ip/hotspot/user/remove'))->equal('.id', $id);
        $this->client->query($remove)->read();

        return true;
    }

    /**
     * Force-disconnect an active hotspot session (kick a connected device).
     */
    public function kickActiveSession(string $voucherCode): bool
    {
        $find = (new Query('/ip/hotspot/active/print'))
            ->where('user', $voucherCode);
        $active = $this->client->query($find)->read();

        foreach ($active as $session) {
            $remove = (new Query('/ip/hotspot/active/remove'))->equal('.id', $session['.id']);
            $this->client->query($remove)->read();
        }

        return true;
    }

    /** List currently connected (active) hotspot sessions — for the admin dashboard. */
    public function listActiveSessions(): array
    {
        $query = new Query('/ip/hotspot/active/print');
        return $this->client->query($query)->read();
    }

    /**
     * Bind a voucher to the MAC address that first used it, so the same
     * code can't be reused on a second device (enforces shared_users=1
     * at the account level via mac-cookie / mac binding on RouterOS).
     */
    public function bindMacAddress(string $voucherCode, string $mac): bool
    {
        $find = (new Query('/ip/hotspot/user/print'))->where('name', $voucherCode);
        $result = $this->client->query($find)->read();
        if (empty($result)) {
            throw new RuntimeException("Voucher {$voucherCode} not found on router");
        }

        $update = (new Query('/ip/hotspot/user/set'))
            ->equal('.id', $result[0]['.id'])
            ->equal('mac-address', $mac);
        $this->client->query($update)->read();

        return true;
    }

    private function formatUptime(?int $minutes): string
    {
        if (! $minutes) return '00:00:00'; // 0 = unlimited on RouterOS
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d:00', $h, $m);
    }

    private function formatBytes(?int $mb): string
    {
        return $mb ? (string) ($mb * 1024 * 1024) : '0'; // 0 = unlimited
    }
}
