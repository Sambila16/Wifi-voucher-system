@extends('admin.layout')
@section('title', 'Routers')

@section('content')

<div class="section-header">
  <h2 style="font-size: 15px; margin: 0;">Add a router</h2>
</div>

@if ($errors->any())
  <div class="flash error">
    <ul style="margin: 0; padding-left: 18px;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
<div class="card" style="margin-bottom: 28px;">
  <form method="POST" action="{{ route('admin.routers.store') }}" class="form-grid">
    @csrf
    <div>
      <label for="name">Name</label>
      <input type="text" name="name" id="name" placeholder="AquaGold Main Hotspot" required>
    </div>
    <div>
      <label for="ip_address">Router IP</label>
      <input type="text" name="ip_address" id="ip_address" placeholder="192.168.88.1" required>
    </div>
    <div>
      <label for="api_port">API port</label>
      <input type="number" name="api_port" id="api_port" value="8728" required>
    </div>
    <div>
      <label for="username">API username</label>
      <input type="text" name="username" id="username" required>
    </div>
    <div>
      <label for="password">API password</label>
      <input type="password" name="password" id="password" required>
    </div>
    <div>
      <label for="hotspot_server">Hotspot server name</label>
      <input type="text" name="hotspot_server" id="hotspot_server" value="hotspot1" required>
    </div>
    <div>
      <label for="default_profile">Default (unpaid) profile</label>
      <input type="text" name="default_profile" id="default_profile" value="default-unpaid" required>
    </div>
    <div style="align-self: end;">
      <button type="submit" class="btn-primary" style="width: 100%;">Add router</button>
    </div>
  </form>
</div>

<div class="section-header">
  <h2 style="font-size: 15px; margin: 0;">All routers</h2>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>IP address</th>
        <th>Hotspot server</th>
        <th>Last connected</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($routers as $router)
        <tr>
          <td>{{ $router->name }}</td>
          <td><code>{{ $router->ip_address }}:{{ $router->api_port }}</code></td>
          <td>{{ $router->hotspot_server }}</td>
          <td>{{ $router->last_connected_at?->diffForHumans() ?? 'Never' }}</td>
          <td>
            <form class="inline" method="POST" action="{{ route('admin.routers.test', $router) }}">
              @csrf
              <button type="submit" class="btn-small">Test connection</button>
            </form>
            <a href="{{ route('admin.routers.sessions', $router) }}" class="btn-small" style="display: inline-block; margin-left: 6px; background: #48626C;">View sessions</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" style="color: var(--muted);">No routers yet — add one above.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection
