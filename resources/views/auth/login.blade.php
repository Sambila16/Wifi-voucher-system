<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in — AquaGold Admin</title>
<style>
  :root {
    --ink: #16232B;
    --line: #DCE3E7;
    --accent: #1E5F74;
    --error: #B23A3A;
    --bg: #F5F7F8;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    background: var(--bg);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
  }
  .card {
    width: 100%;
    max-width: 360px;
    padding: 32px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 10px;
  }
  h1 { font-size: 19px; margin: 0 0 4px; }
  .sub { font-size: 13px; color: #667680; margin: 0 0 24px; }
  label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
  input {
    width: 100%;
    padding: 10px 12px;
    font-size: 15px;
    border: 1px solid #C6D0D4;
    border-radius: 7px;
    margin-bottom: 16px;
    font-family: inherit;
  }
  input:focus { outline: 2px solid var(--accent); outline-offset: 1px; }
  .btn {
    width: 100%;
    padding: 11px;
    font-size: 15px;
    font-weight: 700;
    border: none;
    border-radius: 7px;
    background: var(--accent);
    color: #fff;
    cursor: pointer;
    font-family: inherit;
  }
  .error { color: var(--error); font-size: 13px; margin: -8px 0 16px; }
</style>
</head>
<body>
  <div class="card">
    <h1>AquaGold Admin</h1>
    <p class="sub">Sign in to manage vouchers and routers.</p>

    @if ($errors->any())
      <p class="error">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
      @csrf
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit" class="btn">Sign in</button>
    </form>
  </div>
</body>
</html>
