@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="stat-row">
  <div class="stat stat-revenue"><div class="stat-heading"><p class="label">Revenue today</p><span class="stat-icon">TZS</span></div><p class="value">{{ number_format($today) }} TZS</p><p class="stat-detail">Completed payments today</p></div>
  <div class="stat stat-revenue"><div class="stat-heading"><p class="label">Revenue this month</p><span class="stat-icon">TZS</span></div><p class="value">{{ number_format($thisMonth) }} TZS</p><p class="stat-detail">Since {{ now()->startOfMonth()->format('d M') }}</p></div>
  <div class="stat"><div class="stat-heading"><p class="label">Active vouchers</p><span class="stat-icon">✓</span></div><p class="value">{{ $activeVouchers }}</p><p class="stat-detail">Ready for customer use</p></div>
  <div class="stat"><div class="stat-heading"><p class="label">Needs attention</p><span class="stat-icon">!</span></div><p class="value" style="color: {{ $pendingVouchers > 0 ? '#B23A3A' : 'inherit' }}">{{ $pendingVouchers }}</p><p class="stat-detail">Voucher delivery issues</p></div>
</div>

@if ($pendingVouchers > 0)
  <div class="flash error">{{ $pendingVouchers }} voucher(s) were paid for but failed to reach the router — check <a href="{{ route('admin.vouchers.index') }}" style="text-decoration: underline;">Vouchers</a> and retry them.</div>
@endif

<div class="dashboard-grid">
  <section class="dashboard-panel revenue-panel">
    <div class="section-header"><div><p class="eyebrow">Performance</p><h2>Revenue over the last 7 days</h2></div><span class="subtle-total">{{ number_format($weeklyRevenue->sum('total')) }} TZS</span></div>
    <div class="chart" aria-label="Revenue for the last seven days">
      @php($highestDailyRevenue = max(1, $weeklyRevenue->max('total')))
      @foreach ($weeklyRevenue as $day)
        <div class="chart-column"><span class="chart-value">{{ $day['total'] > 0 ? number_format($day['total']) : '' }}</span><div class="chart-track"><div class="chart-bar" style="height: {{ max(5, ($day['total'] / $highestDailyRevenue) * 100) }}%"></div></div><span class="chart-label">{{ $day['label'] }}</span></div>
      @endforeach
    </div>
  </section>
  <section class="dashboard-panel quick-actions">
    <p class="eyebrow">Shortcuts</p><h2>Quick actions</h2>
    <a href="{{ route('admin.vouchers.index') }}" class="quick-action"><span class="quick-action-icon">+</span><span><strong>Create voucher</strong><small>Issue access for a customer</small></span><span aria-hidden="true">›</span></a>
    <a href="{{ route('admin.plans.index') }}" class="quick-action"><span class="quick-action-icon">⌁</span><span><strong>Manage plans</strong><small>{{ $activePlans }} active plan{{ $activePlans === 1 ? '' : 's' }}</small></span><span aria-hidden="true">›</span></a>
    <a href="{{ route('admin.routers.index') }}" class="quick-action"><span class="quick-action-icon">◉</span><span><strong>Check routers</strong><small>{{ $activeRouters }} active router{{ $activeRouters === 1 ? '' : 's' }}</small></span><span aria-hidden="true">›</span></a>
  </section>
</div>

<div class="section-header"><div><p class="eyebrow">Latest activity</p><h2>Recent payments</h2></div><a href="{{ route('admin.vouchers.index') }}" class="text-link">View vouchers →</a></div>
<div class="card"><table><thead><tr><th>Plan</th><th>Amount</th><th>Gateway</th><th>Voucher</th><th>Paid at</th></tr></thead><tbody>
  @forelse ($recentPayments as $payment)
    <tr><td><strong>{{ $payment->plan?->name ?? 'Deleted plan' }}</strong></td><td><strong>{{ number_format($payment->amount) }} TZS</strong></td><td><span class="badge badge-neutral">{{ ucfirst($payment->gateway) }}</span></td><td class="voucher-code">{{ $payment->voucher?->code ?? '—' }}</td><td>{{ $payment->paid_at?->diffForHumans() }}</td></tr>
  @empty
    <tr><td colspan="5" style="color: var(--muted);">No payments yet.</td></tr>
  @endforelse
</tbody></table></div>

@if ($revenueByGateway->isNotEmpty())
  <div class="section-header section-spacing"><div><p class="eyebrow">Payment channels</p><h2>Last 30 days by payment method</h2></div></div>
  <div class="card"><table><thead><tr><th>Gateway</th><th>Total</th></tr></thead><tbody>@foreach ($revenueByGateway as $row)<tr><td><span class="badge badge-neutral">{{ ucfirst($row->gateway) }}</span></td><td><strong>{{ number_format($row->total) }} TZS</strong></td></tr>@endforeach</tbody></table></div>
@endif
@endsection
