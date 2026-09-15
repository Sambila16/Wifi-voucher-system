<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Voucher;
use App\Services\PaymentGateways\PaymentGatewayInterface;
use App\Services\VoucherService;
use Illuminate\Http\Request;

/**
 * Everything a device sitting on the WiFi (walled garden only — no real
 * internet yet) can reach: the plan-selection page, "I already have a
 * voucher" login, and the payment gateway webhook.
 */
class HotspotController extends Controller
{
    public function __construct(private VoucherService $voucherService) {}

    /**
     * MikroTik hotspot redirects unauthenticated devices here
     * (as the "login" walled-garden page). Shows available plans.
     */
    public function landing(Request $request)
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        // MikroTik passes these as query params on the redirect
        $mac = $request->query('mac');
        $ip = $request->query('ip');
        $linkLoginOnly = $request->query('link-login-only');

        return view('hotspot.landing', compact('plans', 'mac', 'ip', 'linkLoginOnly'));
    }

    /** Customer already has a voucher code (manual/printed) — log in directly. */
    public function loginWithVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'mac' => 'required|string',
            'link-login-only' => 'required|url',
        ]);

        $voucher = Voucher::where('code', $data['code'])->first();

        if (! $voucher || ! $voucher->isUsable()) {
            return back()->withErrors(['code' => 'Invalid, expired, or already-used voucher code.']);
        }

        // Enforce one-device-per-voucher: if already bound to a different MAC, reject.
        if ($voucher->mac_address && $voucher->mac_address !== $data['mac']) {
            return back()->withErrors(['code' => 'This voucher is already in use on another device.']);
        }

        $this->voucherService->markActivated($voucher, $data['mac']);

        // Submit MikroTik's own hotspot login form so the router grants access.
        return view('hotspot.submit-login', [
            'action' => $data['link-login-only'],
            'username' => $voucher->code,
            'password' => $voucher->password,
        ]);
    }

    /** Customer picks a plan and pays via mobile money — initiate the push prompt. */
       public function initiatePayment(Request $request, PaymentGatewayInterface $gateway)
{
    $data = $request->validate([
        'plan_id' => 'required|exists:plans,id',
        'phone' => 'required|string|max:20',
        'network' => 'required|in:yas,vodacom,airtel,halotel',
    ]);

    $plan = Plan::findOrFail($data['plan_id']);

    $payment = Payment::create([
        'plan_id' => $plan->id,
        'amount' => $plan->price,
        'method' => 'mobile_money',
        'gateway' => config('services.payment_gateway.default', 'manual'),
        'customer_phone' => $data['phone'],
        'mobile_network' => $data['network'],
        'status' => 'pending',
    ]);
        $reference = $gateway->initiate($data['phone'], (float) $plan->price, "payment-{$payment->id}");
        $payment->update(['gateway_reference' => $reference]);

        return response()->json([
            'message' => 'Payment request sent to your phone. Approve it to get connected.',
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Gateway webhook. This is the ONLY place a voucher gets auto-created
     * from a mobile money payment — always verify the signature first.
     */
    public function paymentWebhook(Request $request, PaymentGatewayInterface $gateway)
    {
        $rawBody = $request->getContent();

        if (! $gateway->verifyWebhookSignature($request->headers->all(), $rawBody)) {
            abort(401, 'Invalid webhook signature.');
        }

        $result = $gateway->parseWebhookPayload($request->all());

        $payment = Payment::where('gateway_reference', $result['reference'])->first();
        if (! $payment) {
            abort(404, 'Unknown payment reference.');
        }

        $payment->update([
            'status' => $result['status'],
            'gateway_payload' => $request->all(),
            'paid_at' => $result['status'] === 'completed' ? now() : null,
        ]);

        if ($result['status'] === 'completed') {
            $voucher = $this->voucherService->generateForPayment($payment);
            // TODO once gateway is chosen: send $voucher->code to customer_phone via SMS.
        }

        return response()->json(['received' => true]);
    }

    /** Frontend polls this after initiatePayment() to know when to show the voucher/auto-login. */
    public function paymentStatus(Payment $payment)
    {
        return response()->json([
            'status' => $payment->status,
            'voucher_code' => $payment->voucher?->code,
        ]);
    }
}
