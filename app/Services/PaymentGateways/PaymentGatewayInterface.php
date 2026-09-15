<?php

namespace App\Services\PaymentGateways;

use App\Models\Payment;

/**
 * Any automatic payment gateway (Azampay, Selcom, ClickPesa, ...) implements
 * this. VoucherService never talks to a gateway directly — it only depends
 * on this interface, so swapping providers later is a one-file change.
 */
interface PaymentGatewayInterface
{
    /**
     * Initiate a payment request (e.g. push-USSD / STK-style prompt) for
     * the given phone number and amount. Returns a gateway reference to
     * track the transaction.
     */
    public function initiate(string $phone, float $amount, string $reference): string;

    /**
     * Verify a webhook/callback payload actually came from the gateway
     * (signature check) before trusting it.
     */
    public function verifyWebhookSignature(array $headers, string $rawBody): bool;

    /**
     * Parse a webhook payload into a normalized result so the caller
     * doesn't need to know the gateway's specific field names.
     *
     * @return array{reference: string, status: string, amount: float, phone: string}
     */
    public function parseWebhookPayload(array $payload): array;
}
