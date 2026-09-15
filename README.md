# AquaGold WiFi Voucher System

A MikroTik hotspot voucher system: manual (cash) and automatic (mobile
money) vouchers, enforced so nobody gets internet without paying, plus
an admin panel.

## How it fits together

```
Customer's phone
      │  connects to WiFi (gets IP, but walled-garden only — see mikrotik-setup.rsc)
      ▼
MikroTik router  ──redirects──▶  /hotspot  (resources/views/hotspot/landing.blade.php)
      │                                │
      │                         picks a plan, pays via mobile money
      │                                │
      │                         POST /hotspot/pay ──▶ PaymentGatewayInterface::initiate()
      │                                │
      │                    gateway sends webhook when customer approves
      │                                │
      │                         POST /webhooks/payment
      │                                │
      │                    VoucherService::generateForPayment()
      │                                │
      │                    MikrotikService::createHotspotUser()  ◀── ONLY happens here
      │                                │
      │◀── voucher code returned to phone, auto-submits MikroTik's own login form
      ▼
  Real internet access granted (RouterOS hotspot user now exists)
```

Manual (cash) vouchers follow the same path, just starting from the
admin panel (`Admin\VoucherController::storeManual`) instead of a
webhook — the payment is marked "completed" immediately since the
cashier is holding the cash.

**The core rule:** `MikrotikService::createHotspotUser()` is the only
place a device becomes able to get online, and it's only ever called
from `VoucherService::generateForPayment()`, which refuses to run
unless the linked `Payment` is `status = completed`. There's no path
from "device joins WiFi" to "device gets internet" that skips payment.

## Setup

1. **Router**: run `mikrotik-setup.rsc` on your MikroTik (Winbox → New
   Terminal → paste, or import as a script). This creates the walled
   garden that blocks unpaid devices from real internet and points
   your hotspot at the Laravel login page.

2. **Laravel**:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```
   Then in the admin panel (`/admin/routers`), add your router's IP,
   API port (8728, or 8729 if `use_ssl`), and RouterOS API credentials
   (create a dedicated API user on the router — don't reuse `admin`).

3. **Plans**: add your pricing tiers in `/admin/plans` — each needs a
   `mikrotik_profile` name that matches a hotspot user profile that
   exists on the router (rate limit, etc. beyond the base walled-garden
   one).

4. **Payment gateway** (when you've picked one — Azampay / Selcom /
   ClickPesa): see the comment block at the bottom of
   `app/Services/PaymentGateways/ManualPaymentGateway.php` for the
   4-step checklist to add a real one. Until then, the system works
   fully with manual/cash vouchers.

## Logging into the admin panel

There's no registration page on purpose — create your first admin user
via `php artisan tinker`:

```php
User::create(['name'=>'Denisi','email'=>'you@example.com','password'=>bcrypt('yourpassword'),'role'=>'super_admin']);
```

Then visit `/login`. This is a minimal hand-rolled login (no Breeze/
Jetstream) — just enough to protect `/admin/*`. In `bootstrap/app.php`,
make sure the `admin` middleware alias is registered and
`webhooks/payment` is excluded from CSRF checks:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);

    $middleware->validateCsrfTokens(except: [
        'webhooks/payment',
    ]);
})
```

## Security notes

- Router credentials are stored encrypted (`Router::password_encrypted`,
  via Laravel's `Crypt`), never in plaintext.
- The payment webhook **must** verify the gateway's signature
  (`verifyWebhookSignature`) before trusting any "payment completed"
  claim — this is the one thing that MUST NOT be skipped once you wire
  up a real gateway, or anyone could POST a fake success and get free
  vouchers.
- Each voucher gets MAC-bound on first use (`bindMacAddress`), so a
  code can't be shared across multiple devices.
- `.env` (DB credentials, encryption key, future gateway API keys)
  must never be committed — it's already in `.gitignore` in a standard
  Laravel install.
