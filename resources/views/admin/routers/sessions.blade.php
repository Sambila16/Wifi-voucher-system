@extends('admin.layout')
@section('title', 'Active sessions — ' . $router->name)

@section('content')

<p style="margin-top: -10px; margin-bottom: 20px;">
  <a href="{{ route('admin.routers.index') }}" style="color: var(--accent); text-decoration: underline;">← Back to routers</a>
</p>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Voucher / user</th>
        <th>IP address</th>
        <th>MAC address</th>
        <th>Uptime</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($sessions as $session)
        <tr>
          <td><code>{{ $session['user'] ?? '—' }}</code></td>
          <td>{{ $session['address'] ?? '—' }}</td>
          <td>{{ $session['mac-address'] ?? '—' }}</td>
          <td>{{ $session['uptime'] ?? '—' }}</td>
          <td>
            @if (! empty($session['user']))
              <form class="inline" method="POST" action="{{ route('admin.routers.kick', [$router, $session['user']]) }}"
                    onsubmit="return confirm('Disconnect this device now?');">
                @csrf
                <button type="submit" class="btn-small danger">Disconnect</button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="5" style="color: var(--muted);">No devices currently connected.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection
