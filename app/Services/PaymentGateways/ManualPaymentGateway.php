<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Str;

/**
 * Not a real gateway — used when an admin/cashier records a cash sale.
 * "Initiating" just means generating a reference; there's no external
 * call and no webhook, the admin marks it completed directly.
 */
class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function initiate(string $phone, float $amount, string $reference): string
    {
        return 'MANUAL-' . Str::upper(Str::random(10));
    }

    public function verifyWebhookSignature(array $headers, string $rawBody): bool
    {
        return true; // no webhooks for manual payments
    }

    public function parseWebhookPayload(array $payload): array
    {
        return [
            'reference' => $payload['reference'] ?? '',
            'status' => 'completed',
            'amount' => $payload['amount'] ?? 0,
            'phone' => $payload['phone'] ?? '',
        ];
    }
}

/*
 * -----------------------------------------------------------------------
 * TO ADD A REAL GATEWAY LATER (Azampay / Selcom / ClickPesa):
 *
 * 1. Create app/Services/PaymentGateways/AzampayGateway.php (etc.)
 *    implementing PaymentGatewayInterface.
 * 2. initiate() calls their "checkout"/"push USSD" endpoint with your
 *    API key (put keys in .env, never commit them).
 * 3. verifyWebhookSignature() checks their HMAC/signature header against
 *    a secret from .env — REQUIRED, otherwise anyone could fake a
 *    "payment succeeded" webhook and get free vouchers.
 * 4. parseWebhookPayload() maps their JSON fields to the normalized
 *    array shape above.
 * 5. Bind it in a service provider:
 *    $this->app->bind(PaymentGatewayInterface::class, AzampayGateway::class);
 * -----------------------------------------------------------------------
 */
