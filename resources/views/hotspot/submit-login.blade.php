<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Connecting…</title>
<style>
  body { font-family: -apple-system, sans-serif; background: #F2F7F6; color: #142B2A;
         display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
  p { font-size: 15px; }
</style>
</head>
<body>
  <p>Connecting you to the internet…</p>
  <!-- MikroTik's hotspot expects a standard POST with username/password to its own login URL. -->
  <form id="mtLogin" method="POST" action="{{ $action }}">
    <input type="hidden" name="username" value="{{ $username }}">
    <input type="hidden" name="password" value="{{ $password }}">
  </form>
  <script>document.getElementById('mtLogin').submit();</script>
</body>
</html>
