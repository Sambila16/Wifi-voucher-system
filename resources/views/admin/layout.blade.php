<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') — AquaGold Admin</title>
<style>
  :root {
    --ink: #16232B;
    --muted: #667680;
    --line: #E2E8EB;
    --accent: #1E5F74;
    --accent-light: #E8F1F3;
    --gold: #B8860B;
    --success: #2F7D5E;
    --error: #B23A3A;
    --bg: #F5F7F8;
    --sidebar-w: 220px;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: var(--ink);
    background: var(--bg);
  }
  a { color: inherit; text-decoration: none; }

  .sidebar {
    position: fixed;
    top: 0; left: 0; bottom: 0;
    width: var(--sidebar-w);
    background: #fff;
    border-right: 1px solid var(--line);
    padding: 24px 16px;
  }
  .sidebar .brand {
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 28px;
    padding: 0 8px;
  }
  .sidebar .brand span { color: var(--gold); }
  .nav-item {
    display: block;
    padding: 9px 10px;
    border-radius: 7px;
    font-size: 14px;
    font-weight: 500;
    color: var(--muted);
    margin-bottom: 2px;
  }
  .nav-item:hover { background: var(--bg); color: var(--ink); }
  .nav-item.active { background: var(--accent-light); color: var(--accent); font-weight: 700; }

  .main {
    margin-left: var(--sidebar-w);
    padding: 28px 36px 60px;
    max-width: 1320px;
  }
  .topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  .topbar h1 { font-size: 25px; letter-spacing: -0.4px; margin: 0; }
  .topbar form { margin: 0; }
  .logout-btn {
    background: none;
    border: 1px solid var(--line);
    padding: 7px 14px;
    border-radius: 7px;
    font-size: 13px;
    cursor: pointer;
    color: var(--muted);
    font-family: inherit;
  }
  .logout-btn:hover { border-color: var(--muted); color: var(--ink); }

  .flash {
    padding: 11px 15px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 20px;
  }
  .flash.success { background: #E6F3EC; color: var(--success); }
  .flash.error { background: #FBEAEA; color: var(--error); }

  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  th {
    text-align: left;
    font-size: 12px;
    text-transform: none;
    color: var(--muted);
    font-weight: 600;
    padding: 10px 12px;
    border-bottom: 1px solid var(--line);
  }
  td {
    padding: 12px;
    border-bottom: 1px solid var(--line);
  }
  .card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 10px;
    overflow: hidden;
  }

  .stat-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
  .stat {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 16px 18px;
  }
  .stat .label { font-size: 12px; color: var(--muted); margin: 0; }
  .stat .value { font-size: 24px; font-weight: 700; margin: 0; }
  .stat-heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
  .stat-icon { display: grid; place-items: center; width: 25px; height: 25px; border-radius: 7px; background: var(--accent-light); color: var(--accent); font-size: 10px; font-weight: 800; }
  .stat-detail { margin: 7px 0 0; color: var(--muted); font-size: 11px; }
  .stat-revenue .stat-icon { color: var(--gold); background: #FDF3E0; }

  .badge {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }
  .badge.issued, .badge.active, .badge.completed { background: #E6F3EC; color: var(--success); }
  .badge.pending { background: #FDF3E0; color: var(--gold); }
  .badge.revoked, .badge.expired, .badge.failed { background: #FBEAEA; color: var(--error); }

  form.inline { display: inline; }
  .btn-small {
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    font-family: inherit;
  }
  .btn-small.danger { background: var(--error); }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
  }
  .section-header h2, .dashboard-panel h2 { margin: 2px 0 0; font-size: 16px; letter-spacing: -0.15px; }
  .eyebrow { color: var(--accent); font-size: 10px; font-weight: 800; letter-spacing: .08em; margin: 0; text-transform: uppercase; }
  .section-spacing { margin-top: 28px; }
  .text-link { color: var(--accent); font-size: 12px; font-weight: 700; }
  .text-link:hover { text-decoration: underline; }
  .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(280px, .9fr); gap: 16px; margin-bottom: 30px; }
  .dashboard-panel { background: #fff; border: 1px solid var(--line); border-radius: 10px; padding: 18px; }
  .subtle-total { color: var(--muted); font-size: 12px; font-weight: 700; }
  .chart { height: 165px; display: flex; align-items: end; gap: 12px; padding-top: 16px; }
  .chart-column { height: 100%; flex: 1; min-width: 0; display: flex; flex-direction: column; align-items: center; }
  .chart-value { height: 16px; color: var(--muted); font-size: 9px; white-space: nowrap; }
  .chart-track { flex: 1; width: 100%; max-width: 42px; display: flex; align-items: end; background: #F1F5F6; border-radius: 5px 5px 2px 2px; overflow: hidden; }
  .chart-bar { width: 100%; min-height: 4px; background: linear-gradient(180deg, #318099, var(--accent)); border-radius: 4px 4px 1px 1px; }
  .chart-label { color: var(--muted); font-size: 10px; margin-top: 7px; }
  .quick-actions h2 { margin-bottom: 12px; }
  .quick-action { display: grid; grid-template-columns: 28px 1fr auto; gap: 10px; align-items: center; padding: 10px 0; border-top: 1px solid var(--line); color: var(--muted); font-size: 18px; }
  .quick-action:hover strong { color: var(--accent); }
  .quick-action-icon { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 7px; color: var(--accent); background: var(--accent-light); font-size: 15px; font-weight: 700; }
  .quick-action strong, .quick-action small { display: block; }
  .quick-action strong { color: var(--ink); font-size: 12px; }
  .quick-action small { color: var(--muted); font-size: 10px; margin-top: 2px; }
  .badge-neutral { background: var(--accent-light); color: var(--accent); }
  .voucher-code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; }
  .btn-primary {
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 9px 16px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    padding: 18px;
  }
  .form-grid label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
  .form-grid input, .form-grid select {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #C6D0D4;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
  }
  @media (max-width: 900px) { .stat-row { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dashboard-grid { grid-template-columns: 1fr; } }
  @media (max-width: 650px) {
    :root { --sidebar-w: 0px; }
    .sidebar { position: static; width: 100%; padding: 14px 16px; display: flex; align-items: center; gap: 4px; overflow-x: auto; }
    .sidebar .brand { margin: 0 12px 0 0; padding: 0; white-space: nowrap; }
    .nav-item { margin: 0; white-space: nowrap; }
    .main { margin-left: 0; padding: 22px 16px 42px; }
    .topbar { margin-bottom: 20px; }
    .stat-row { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat { padding: 14px; }
    .stat .value { font-size: 20px; }
    .card { overflow-x: auto; }
    table { min-width: 650px; }
  }
</style>
</head>
<body>

<div class="sidebar">
  <div class="brand">Aqua<span>Gold</span></div>
  <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
  <a href="{{ route('admin.vouchers.index') }}" class="nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">Vouchers</a>
  <a href="{{ route('admin.plans.index') }}" class="nav-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">Plans</a>
  <a href="{{ route('admin.routers.index') }}" class="nav-item {{ request()->routeIs('admin.routers.*') ? 'active' : '' }}">Routers</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>@yield('title', 'Dashboard')</h1>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="logout-btn">Sign out</button>
    </form>
  </div>

  @if (session('success'))
    <div class="flash success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="flash error">{{ session('error') }}</div>
  @endif

  @yield('content')
</div>

</body>
</html>
