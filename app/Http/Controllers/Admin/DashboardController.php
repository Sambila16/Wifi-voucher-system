<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Router;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Payment::whereDate('paid_at', today())
            ->where('status', 'completed')
            ->sum('amount');

        $thisMonth = Payment::whereMonth('paid_at', now()->month)
            ->where('status', 'completed')
            ->sum('amount');

        $activeVouchers = Voucher::whereIn('status', ['issued', 'active'])->count();
        $pendingVouchers = Voucher::where('status', 'pending')->count(); // paid but router push failed — needs attention

        $recentPayments = Payment::with(['plan', 'voucher'])
            ->where('status', 'completed')
            ->latest('paid_at')
            ->limit(10)
            ->get();

        $revenueByGateway = Payment::where('status', 'completed')
            ->whereDate('paid_at', '>=', now()->subDays(30))
            ->select('gateway', DB::raw('SUM(amount) as total'))
            ->groupBy('gateway')
            ->get();

        $revenueByDay = Payment::where('status', 'completed')
            ->whereDate('paid_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $weeklyRevenue = collect(range(6, 0))->map(function ($daysAgo) use ($revenueByDay) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->isToday() ? 'Today' : $date->format('D'),
                'total' => (float) ($revenueByDay[$date->toDateString()] ?? 0),
            ];
        });

        $activePlans = Plan::where('is_active', true)->count();
        $activeRouters = Router::where('is_active', true)->count();

        return view('admin.dashboard', compact(
            'today', 'thisMonth', 'activeVouchers', 'pendingVouchers',
            'recentPayments', 'revenueByGateway', 'weeklyRevenue',
            'activePlans', 'activeRouters'
        ));
    }
}
