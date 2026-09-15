@extends('admin.layout')
@section('title', 'Plans')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="section-header">
  <h2 style="font-size: 15px; margin: 0;">Add a plan</h2>
</div>
<div class="card" style="margin-bottom: 28px;">
  <form method="POST" action="{{ route('admin.plans.store') }}" class="form-grid">
    @csrf
    <div>
      <label for="name">Name</label>
      <input type="text" name="name" id="name" placeholder="2GB / 24 Hours" required>
    </div>
    <div>
      <label for="price">Price (TZS)</label>
      <input type="number" name="price" id="price" min="0" required>
    </div>
    <div>
      <label for="data_limit_mb">Data limit (MB, blank = unlimited)</label>
      <input type="number" name="data_limit_mb" id="data_limit_mb" min="1">
    </div>
    <div>
      <label for="time_limit_minutes">Time limit (minutes, blank = unlimited)</label>
      <input type="number" name="time_limit_minutes" id="time_limit_minutes" min="1">
    </div>
    <div>
      <label for="validity_days">Validity if unused (days)</label>
      <input type="number" name="validity_days" id="validity_days" min="1" value="1" required>
    </div>
    <div>
      <label for="mikrotik_profile">MikroTik hotspot profile name</label>
      <input type="text" name="mikrotik_profile" id="mikrotik_profile" placeholder="must match a profile on the router" required>
    </div>
    <div>
      <label for="shared_users">Devices per voucher</label>
      <input type="number" name="shared_users" id="shared_users" min="1" value="1" required>
    </div>
    <div style="align-self: end;">
      <button type="submit" class="btn-primary" style="width: 100%;">Create plan</button>
    </div>
  </form>
</div>

<div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
  <h2 style="font-size: 15px; margin: 0;">All plans</h2>
  <a href="#" id="toggle-disabled-link" onclick="toggleDisabledPlans(event);" style="font-size: 13px;">
    Show disabled plans
  </a>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Data</th>
        <th>Time</th>
        <th>MikroTik profile</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($plans as $plan)
        <tr class="{{ $plan->is_active ? '' : 'disabled-plan-row' }}" style="{{ $plan->is_active ? '' : 'display: none;' }}">
          <td>{{ $plan->name }}</td>
          <td>{{ number_format($plan->price) }} TZS</td>
          <td>{{ $plan->data_limit_mb ? number_format($plan->data_limit_mb) . ' MB' : 'Unlimited' }}</td>
          <td>{{ $plan->time_limit_minutes ? number_format($plan->time_limit_minutes) . ' min' : 'Unlimited' }}</td>
          <td><code>{{ $plan->mikrotik_profile }}</code></td>
          <td><span class="badge {{ $plan->is_active ? 'active' : 'expired' }}">{{ $plan->is_active ? 'Active' : 'Disabled' }}</span></td>
          <td>
            <button type="button" class="btn-small" onclick="document.getElementById('edit-row-{{ $plan->id }}').style.display =
                document.getElementById('edit-row-{{ $plan->id }}').style.display === 'none' ? 'table-row' : 'none';">
              Edit
            </button>

            @if ($plan->is_active)
                 <form class="inline" method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                        onsubmit="return confirm('Disable this plan? Existing vouchers are unaffected.');">
                    @csrf
                    @method('DELETE')
                   <button type="submit" class="btn-small danger">Disable</button>
                  </form>
            @else
               <form class="inline" method="POST" action="{{ route('admin.plans.enable', $plan) }}">
                    @csrf
                 <button type="submit" class="btn-small">Enable</button>
               </form>
            @endif

            <form class="inline" method="POST" action="{{ route('admin.plans.force-delete', $plan) }}"
                  onsubmit="return confirm('Permanently delete this plan? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-small danger">Delete</button>
            </form>
          </td>
        </tr>

        {{-- Edit row, hidden by default --}}
        <tr id="edit-row-{{ $plan->id }}" class="{{ $plan->is_active ? '' : 'disabled-plan-row' }}" style="display: none;">
          <td colspan="7">
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="form-grid" style="padding: 12px 0;">
              @csrf
              @method('PUT')
              <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ $plan->name }}" required>
              </div>
              <div>
                <label>Price (TZS)</label>
                <input type="number" name="price" min="0" value="{{ $plan->price }}" required>
              </div>
              <div>
                <label>Data limit (MB, blank = unlimited)</label>
                <input type="number" name="data_limit_mb" min="1" value="{{ $plan->data_limit_mb }}">
              </div>
              <div>
                <label>Time limit (minutes, blank = unlimited)</label>
                <input type="number" name="time_limit_minutes" min="1" value="{{ $plan->time_limit_minutes }}">
              </div>
              <div>
                <label>Validity if unused (days)</label>
                <input type="number" name="validity_days" min="1" value="{{ $plan->validity_days }}" required>
              </div>
              <div>
                <label>MikroTik hotspot profile name</label>
                <input type="text" name="mikrotik_profile" value="{{ $plan->mikrotik_profile }}" required>
              </div>
              <div>
                <label>Devices per voucher</label>
                <input type="number" name="shared_users" min="1" value="{{ $plan->shared_users }}" required>
              </div>
              <div style="align-self: end; display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="width: 100%;">Save</button>
                <button type="button" class="btn-small"
                        onclick="document.getElementById('edit-row-{{ $plan->id }}').style.display = 'none';">
                  Cancel
                </button>
              </div>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" style="color: var(--muted);">No plans yet — add one above.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
  let disabledVisible = false;
  function toggleDisabledPlans(e) {
    e.preventDefault();
    disabledVisible = !disabledVisible;
    document.querySelectorAll('.disabled-plan-row').forEach(function (row) {
      if (row.id && row.id.startsWith('edit-row-')) {
        row.style.display = 'none';
      } else {
        row.style.display = disabledVisible ? 'table-row' : 'none';
      }
    });
    document.getElementById('toggle-disabled-link').textContent =
      disabledVisible ? 'Hide disabled plans' : 'Show disabled plans';
  }
</script>

@endsection