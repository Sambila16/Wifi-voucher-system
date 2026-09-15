<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title>AquaGold WiFi Get connected</title>

<style>

  :root {
    --teal-deep: #0E4749;
    --teal-ink: #142B2A;
    --gold: #C9971F;
    --gold-dim: #E4C878;
    --bg: #F2F7F6;
    --line: #D8E6E3;
    --success: #2F7D5E;
    --error: #B23A3A;
    --accent-light: #E8F1F3;
    --font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    background: var(--bg);
    color: var(--teal-ink);
    font-family: var(--font);
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    position: relative;
  }

  /* Background image with fade-in */
  body::before {
    content: "";
    position: fixed;
    inset: 0;
    background-image: url("https://images.pexels.com/photos/4814732/pexels-photo-4814732.jpeg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    animation: fadeInBg 2.0s ease-out forwards;
    z-index: -2;
  }

  @keyframes fadeInBg {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  /* Soft dark overlay for better contrast */
  body::after {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(14, 71, 73, 0.25);
    z-index: -1;
  }

  .wrap {
    max-width: 480px;
    margin: 0 auto;
    padding: 28px 20px 48px;

    /* Glassmorphism */
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  }

  .brand {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 2px;
  }

  .brand-mark {
    font-size: 21px;
    font-weight: 800;
    letter-spacing: -0.01em;
    color: var(--teal-deep);
  }

  .brand-mark span {
    color: var(--gold);
  }

  .brand-sub {
    font-size: 13px;
    color: #5B7674;
  }

  h1 {
    font-size: 26px;
    line-height: 1.25;
    font-weight: 700;
    letter-spacing: -0.015em;
    margin: 22px 0 6px;
    max-width: 15ch;
  }

  .lede {
    font-size: 15px;
    color: #46605E;
    margin: 0 0 28px;
    max-width: 34ch;
  }

  .plans {
    border-top: 1px solid var(--line);
  }

  .plan {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 4px;
    border-bottom: 1px solid var(--line);
    cursor: pointer;
    background: none;
    border-left: none;
    border-right: none;
    width: 100%;
    text-align: left;
    font-family: inherit;
  }

  .plan-gauge {
    flex: none;
    width: 6px;
    height: 40px;
    border-radius: 3px;
    background: #E1EBE9;
    position: relative;
    overflow: hidden;
  }

  .plan-gauge i {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--teal-deep);
    border-radius: 3px;
    transition: background 0.15s ease;
  }

  .plan.selected .plan-gauge i {
    background: var(--gold);
  }

  .plan-info {
    flex: 1;
    min-width: 0;
  }

  .plan-name {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 2px;
  }

  .plan-meta {
    font-size: 13px;
    color: #5B7674;
    margin: 0;
  }

  .plan-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--teal-deep);
    white-space: nowrap;
  }

  .plan.selected .plan-price {
    color: var(--gold);
  }

  .plan-radio {
    flex: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #B9CDCA;
    position: relative;
  }

  .plan.selected .plan-radio {
    border-color: var(--gold);
  }

  .plan.selected .plan-radio::after {
    content: "";
    position: absolute;
    inset: 3px;
    border-radius: 50%;
    background: var(--gold);
  }

  .pay-section {
    margin-top: 24px;
  }

  label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #3C5654;
    margin-bottom: 6px;
  }

  input[type="tel"],
  input[type="text"] {
    width: 100%;
    padding: 13px 14px;
    font-size: 16px;
    border: 1px solid #C3D6D3;
    border-radius: 8px;
    background: #fff;
    color: var(--teal-ink);
    font-family: inherit;
  }

  input:focus {
    outline: 2px solid var(--teal-deep);
    outline-offset: 1px;
  }

  .btn {
    display: block;
    width: 100%;
    margin-top: 14px;
    padding: 15px 16px;
    font-size: 16px;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    background: var(--teal-deep);
    color: #fff;
    cursor: pointer;
    font-family: inherit;
  }

  .btn:disabled {
    opacity: 0.55;
    cursor: default;
  }

  .btn:focus-visible {
    outline: 3px solid var(--gold);
    outline-offset: 2px;
  }

  .status {
    margin-top: 14px;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: 14px;
    display: none;
  }

  .status.show {
    display: block;
  }

  .status.info {
    background: #E6F0EF;
    color: var(--teal-deep);
  }

  .status.error {
    background: #FBEAEA;
    color: var(--error);
  }

  .status.success {
    background: #E6F3EC;
    color: var(--success);
  }

  .divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 30px 0 18px;
    color: #7E9490;
    font-size: 13px;
  }

  .divider::before,
  .divider::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--line);
  }

  .code-toggle {
    background: none;
    border: none;
    color: var(--teal-deep);
    font-size: 14px;
    font-weight: 600;
    padding: 4px 0;
    cursor: pointer;
    font-family: inherit;
    text-decoration: underline;
    text-underline-offset: 3px;
  }

  .code-panel {
    margin-top: 14px;
    display: none;
  }

  .code-panel.show {
    display: block;
  }

  .footer {
    margin-top: 40px;
    font-size: 12px;
    color: #7E9490;
    text-align: center;
  }

  .network-row {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .network-btn {
    flex: 1;
    min-width: 80px;
    padding: 10px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #C3D6D3;
    border-radius: 8px;
    background: #fff;
    color: var(--teal-ink);
    cursor: pointer;
    font-family: inherit;
  }

  .network-btn.selected {
    border-color: var(--teal-deep);
    background: var(--accent-light);
  }

  /* Code-based network logos */
  .network-logo {
    width: 30px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: none;
  }

  .network-logo svg {
    width: 30px;
    height: 24px;
    display: block;
  }

  .phone-wrap {
    display: flex;
    border: 1px solid #C3D6D3;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 16px;
  }

  .phone-wrap span {
    padding: 13px 10px;
    background: #EEF3F2;
    color: #46605E;
    font-size: 16px;
    border-right: 1px solid #C3D6D3;
  }

  .phone-wrap input {
    border: none;
    border-radius: 0;
    margin-bottom: 0;
  }

</style>

</head>

<body>

<div class="wrap">

  <div class="brand">
    <div class="brand-mark"> Welcome,, Aqua<span>Gold</span> Wi-Fi roaming</div>
  </div>

  <div class="brand-sub">Dar es Salaam</div>

  <h1>Get connected in seconds</h1>

  <p class="lede">
    Choose a plan, pay by mobile money, and you're online no waiting.
  </p>

  <div class="plans" id="plans">

    @foreach ($plans as $index => $plan)

      @php

        $maxPrice = $plans->max('price') ?: 1;

        $fillPct = max(18, round(($plan->price / $maxPrice) * 100));

        $metaParts = [];

        if ($plan->data_limit_mb) {

            $metaParts[] = $plan->data_limit_mb >= 1024
                ? round($plan->data_limit_mb / 1024, 1) . 'GB'
                : $plan->data_limit_mb . 'MB';

        } else {

            $metaParts[] = 'Unlimited data';

        }

        if ($plan->time_limit_minutes) {

            $metaParts[] = $plan->time_limit_minutes >= 1440
                ? round($plan->time_limit_minutes / 1440) . ' days'
                : ($plan->time_limit_minutes >= 60
                    ? round($plan->time_limit_minutes / 60) . ' hours'
                    : $plan->time_limit_minutes . ' minutes');

        }

      @endphp

      <button
        type="button"
        class="plan"
        data-plan-id="{{ $plan->id }}"
        data-plan-name="{{ $plan->name }}"
        data-plan-price="{{ number_format($plan->price) }}"
      >

        <span class="plan-gauge">
          <i style="height:{{ $fillPct }}%"></i>
        </span>

        <span class="plan-info">

          <p class="plan-name">{{ $plan->name }}</p>

          <p class="plan-meta">
            {{ implode(' · ', $metaParts) }}
          </p>

        </span>

        <span class="plan-price">
          {{ number_format($plan->price) }} TZS
        </span>

        <span class="plan-radio"></span>

      </button>

    @endforeach

  </div>

  <div class="pay-section">

    <label>Mobile money network</label>

    <div class="network-row" id="networkRow">

      <!-- YAS -->
      <button
        type="button"
        class="network-btn"
        data-network="yas"
        data-prefixes="065,067,071,077"
      >

        <span class="network-logo">

          <svg
            viewBox="0 0 100 50"
            xmlns="http://www.w3.org/2000/svg"
            aria-label="Yas"
          >

            <rect
              x="2"
              y="2"
              width="96"
              height="46"
              rx="12"
              fill="#1746A2"
            />

            <text
              x="50"
              y="34"
              text-anchor="middle"
              font-family="Arial, sans-serif"
              font-size="25"
              font-weight="800"
              fill="#FFD400"
            >yas</text>

          </svg>

        </span>

        <span>Yas</span>

      </button>


      <!-- VODACOM -->
      <button
        type="button"
        class="network-btn"
        data-network="vodacom"
        data-prefixes="074,075,076,079"
      >

        <span class="network-logo">

          <svg
            viewBox="0 0 100 50"
            xmlns="http://www.w3.org/2000/svg"
            aria-label="Vodacom"
          >

            <rect
              x="2"
              y="2"
              width="96"
              height="46"
              rx="12"
              fill="#E60000"
            />

            <circle
              cx="22"
              cy="25"
              r="12"
              fill="white"
            />

            <circle
              cx="22"
              cy="25"
              r="6"
              fill="#E60000"
            />

            <text
              x="64"
              y="30"
              text-anchor="middle"
              font-family="Arial, sans-serif"
              font-size="13"
              font-weight="700"
              fill="white"
            >vodacom</text>

          </svg>

        </span>

        <span>Vodacom</span>

      </button>


      <!-- AIRTEL -->
      <button
        type="button"
        class="network-btn"
        data-network="airtel"
        data-prefixes="068,069,078"
      >

        <span class="network-logo">

          <svg
            viewBox="0 0 100 50"
            xmlns="http://www.w3.org/2000/svg"
            aria-label="Airtel"
          >

            <rect
              x="2"
              y="2"
              width="96"
              height="46"
              rx="12"
              fill="#E60000"
            />

            <text
              x="50"
              y="31"
              text-anchor="middle"
              font-family="Arial, sans-serif"
              font-size="19"
              font-weight="700"
              fill="white"
            >airtel</text>

          </svg>

        </span>

        <span>Airtel</span>

      </button>


      <!-- HALOTEL -->
      <button
        type="button"
        class="network-btn"
        data-network="halotel"
        data-prefixes="061,062"
      >

        <span class="network-logo">

          <svg
            viewBox="0 0 100 50"
            xmlns="http://www.w3.org/2000/svg"
            aria-label="Halotel"
          >

            <rect
              x="2"
              y="2"
              width="96"
              height="46"
              rx="12"
              fill="#FF7900"
            />

            <text
              x="50"
              y="31"
              text-anchor="middle"
              font-family="Arial, sans-serif"
              font-size="16"
              font-weight="700"
              fill="white"
            >halotel</text>

          </svg>

        </span>

        <span>Halotel</span>

      </button>

    </div>

    <label for="phone">Phone number for mobile money</label>

    <div class="phone-wrap">

      <span>+255</span>

      <input
        type="tel"
        id="phone"
        placeholder="7XX XXX XXX"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="9"
      >

    </div>

    <button
      type="button"
      class="btn"
      id="payBtn"
      disabled
    >
      Select a plan to continue
    </button>

    <div class="status" id="payStatus"></div>

  </div>

  <div class="divider">or</div>

  <button
    type="button"
    class="code-toggle"
    id="codeToggle"
  >
    I already have a voucher code
  </button>

  <form
    class="code-panel"
    id="codePanel"
    method="POST"
    action="{{ route('hotspot.login') }}"
  >

    @csrf

    <input
      type="hidden"
      name="mac"
      value="{{ $mac }}"
    >

    <input
      type="hidden"
      name="link-login-only"
      value="{{ $linkLoginOnly }}"
    >

    <label for="code">Voucher code</label>

    <input
      type="text"
      id="code"
      name="code"
      placeholder="e.g. 7QQ3F9XZ"
      autocapitalize="characters"
    >

    <button
      type="submit"
      class="btn"
      style="background: var(--gold); margin-top: 14px;"
    >
      Connect
    </button>

  </form>

  <p class="footer">
    Need help? Ask any AquaGold attendant nearby.
  </p>

</div>

<script>

  let selectedPlanId = null;
  let selectedNetwork = null;
  let selectedPrefixes = [];

  document.querySelectorAll('.network-btn').forEach(function (btn) {

    btn.addEventListener('click', function () {

      document
        .querySelectorAll('.network-btn')
        .forEach(b => b.classList.remove('selected'));

      btn.classList.add('selected');

      selectedNetwork = btn.dataset.network;

      selectedPrefixes = btn.dataset.prefixes.split(',');

    });

  });


  document.getElementById('phone').addEventListener('input', function () {

    this.value = this.value
      .replace(/\D/g, '')
      .slice(0, 9);

  });


  document.querySelectorAll('.plan').forEach(function (el) {

    el.addEventListener('click', function () {

      document
        .querySelectorAll('.plan')
        .forEach(p => p.classList.remove('selected'));

      el.classList.add('selected');

      selectedPlanId = el.dataset.planId;

      const payBtn = document.getElementById('payBtn');

      payBtn.disabled = false;

      payBtn.textContent =
        'Pay ' +
        el.dataset.planPrice +
        ' TZS for ' +
        el.dataset.planName;

    });

  });


  document
    .getElementById('codeToggle')
    .addEventListener('click', function () {

      document
        .getElementById('codePanel')
        .classList.toggle('show');

    });


  const statusBox = document.getElementById('payStatus');


  function showStatus(kind, text) {

    statusBox.className = 'status show ' + kind;

    statusBox.textContent = text;

  }


  document
    .getElementById('payBtn')
    .addEventListener('click', async function () {

      const phoneRaw = document
        .getElementById('phone')
        .value
        .trim()
        .replace(/\D/g, '');

      if (!selectedPlanId) return;

      if (!selectedNetwork) {

        showStatus(
          'error',
          'Choose your mobile money network.'
        );

        return;

      }

      if (!phoneRaw) {

        showStatus(
          'error',
          'Enter the phone number you will pay with.'
        );

        return;

      }

      if (phoneRaw.length !== 9) {

        showStatus(
          'error',
          'Enter a valid 9-digit number after +255 (e.g. 712345678).'
        );

        return;

      }

      const prefix = '0' + phoneRaw.substring(0, 2);

      if (!selectedPrefixes.includes(prefix)) {

        showStatus(
          'error',
          "That number doesn't match the network you selected. Double check and try again."
        );

        return;

      }

      const fullPhone = '+255' + phoneRaw;

      this.disabled = true;

      const originalText = this.textContent;

      this.textContent = 'Sending payment request…';

      showStatus(
        'info',
        'Check your phone and approve the payment prompt.'
      );

      try {

        const res = await fetch(
          '{{ route('hotspot.pay') }}',
          {
            method: 'POST',

            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json',
            },

            body: JSON.stringify({
              plan_id: selectedPlanId,
              phone: fullPhone,
              network: selectedNetwork
            }),

          }
        );

        const data = await res.json();

        if (!res.ok) {

          showStatus(
            'error',
            data.message || 'Could not start payment. Try again.'
          );

          this.disabled = false;

          this.textContent = originalText;

          return;

        }

        pollPaymentStatus(
          data.payment_id,
          this,
          originalText
        );

      } catch (e) {

        showStatus(
          'error',
          'Network issue — try again.'
        );

        this.disabled = false;

        this.textContent = originalText;

      }

    });


  function pollPaymentStatus(
    paymentId,
    btn,
    originalText
  ) {

    const url =
      '{{ url('/hotspot/payments') }}/' +
      paymentId +
      '/status';


    const interval = setInterval(
      async function () {

        const res = await fetch(
          url,
          {
            headers: {
              'Accept': 'application/json'
            }
          }
        );

        const data = await res.json();


        if (
          data.status === 'completed' &&
          data.voucher_code
        ) {

          clearInterval(interval);

          showStatus(
            'success',
            'Payment received — connecting you now…'
          );

          const form =
            document.getElementById('codePanel');

          document.getElementById('code').value =
            data.voucher_code;

          form.submit();

        }

        else if (data.status === 'failed') {

          clearInterval(interval);

          showStatus(
            'error',
            'Payment was not completed. You can try again.'
          );

          btn.disabled = false;

          btn.textContent = originalText;

        }

      },
      3000
    );


    setTimeout(
      function () {
        clearInterval(interval);
      },
      120000
    );

  }

</script>

</body>

</html>