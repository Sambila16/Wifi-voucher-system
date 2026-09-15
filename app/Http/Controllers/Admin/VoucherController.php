<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function __construct(private VoucherService $voucherService) {}

    public function index()
    {
        $vouchers = Voucher::with(['plan', 'router', 'payment'])
            ->latest()
            ->paginate(25);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Manual voucher generation — a cashier records a cash sale and a
     * voucher is created + pushed to MikroTik in the same request.
     */
    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
           if (! \App\Models\Router::where('is_active', true)->exists()) {
                 return back()->withErrors(['plan_id' => 'Add and activate a router first — vouchers can\'t be issued without one.']);
     }
        // Record the cash payment as already completed — the cashier is
        // physically handing over cash right now.
        $payment = Payment::create([
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'method' => 'cash',
            'gateway' => 'manual',
            'gateway_reference' => 'MANUAL-' . uniqid(),
            'customer_phone' => $data['customer_phone'] ?? null,
            'status' => 'completed',
            'received_by_admin_id' => auth()->id(),
            'paid_at' => now(),
        ]);

        $voucher = $this->voucherService->generateForPayment($payment);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Voucher {$voucher->code} generated.");
    }

    public function revoke(Voucher $voucher)
    {
        $this->voucherService->revoke($voucher);

        return back()->with('success', "Voucher {$voucher->code} revoked.");
    }

    public function retry(Voucher $voucher)
    {
        $ok = $this->voucherService->retryIssue($voucher);

        return back()->with($ok ? 'success' : 'error',
            $ok ? "Voucher {$voucher->code} pushed to router." : "Router unreachable — still pending.");
    }
}
