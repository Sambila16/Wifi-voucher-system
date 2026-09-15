@extends('admin.layout')
@section('title', 'Vouchers')

@section('content')

<div class="section-header">
  <h2 style="font-size: 15px; margin: 0;">Generate a manual (cash) voucher</h2>
</div>
<div class="card" style="margin-bottom: 28px;">
  <form method="POST" action="{{ route('admin.vouchers.manual') }}" class="form-grid" style="align-items: end;">
    @csrf
    <div>
      <label for="plan_id">Plan</label>
      <select name="plan_id" id="plan_id" required>
        <option value="">Select a plan</option>
        @foreach (\App\Models\Plan::where('is_active', true)->get() as $plan)
          <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->price) }} TZS</option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="customer_phone">Customer phone (optional)</label>
      <input type="text" name="customer_phone" id="customer_phone" placeholder="07XX XXX XXX">
    </div>
    <div>
      <button type="submit" class="btn-primary" style="width: 100%;">Generate voucher</button>
    </div>
  </form>
</div>

<div class="section-header">
  <h2 style="font-size: 15px; margin: 0;">All vouchers</h2>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Code</th>
        <th>Plan</th>
        <th>Source</th>
        <th>Status</th>
        <th>Router</th>
        <th>Expires</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($vouchers as $voucher)
        <tr>
          <td><code>{{ $voucher->code }}</code></td>
          <td>{{ $voucher->plan->name }}</td>
          <td>{{ ucfirst($voucher->source) }}</td>
          <td><span class="badge {{ $voucher->status }}">{{ ucfirst($voucher->status) }}</span></td>
          <td>{{ $voucher->router->name }}</td>
          <td>{{ $voucher->expires_at?->format('d M, H:i') ?? '—' }}</td>
          <td>
            @if ($voucher->status === 'pending')
              <form class="inline" method="POST" action="{{ route('admin.vouchers.retry', $voucher) }}">
                @csrf
                <button type="submit" class="btn-small">Retry push</button>
              </form>
            @endif
            @if (in_array($voucher->status, ['issued', 'active']))
              <form class="inline" method="POST" action="{{ route('admin.vouchers.revoke', $voucher) }}"
                    onsubmit="return confirm('Revoke this voucher? The device will lose internet immediately.');">
                @csrf
                <button type="submit" class="btn-small danger">Revoke</button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="7" style="color: var(--muted);">No vouchers yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top: 16px;">
  {{ $vouchers->links() }}
</div>

@endsection
