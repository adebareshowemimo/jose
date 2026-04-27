<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    private const API = 'https://api.paystack.co';

    /**
     * Currencies that bill in the major unit (no kobo/cents conversion needed).
     * Paystack always wants the smallest unit so we always multiply by 100,
     * but document this in case a future currency needs different handling.
     */
    public function isConfigured(): bool
    {
        return ! empty(setting('paystack.secret_key'))
            && ! empty(setting('paystack.public_key'))
            && (bool) setting('paystack.enabled', false);
    }

    public function publicKey(): ?string
    {
        return setting('paystack.public_key');
    }

    /**
     * Initialize a transaction. Returns Paystack response data on success.
     *
     * @return array{authorization_url: string, access_code: string, reference: string}|null
     */
    public function initialize(string $email, float $amount, string $currency, string $reference, string $callbackUrl, array $metadata = []): ?array
    {
        $secret = setting('paystack.secret_key');
        if (! $secret) {
            Log::warning('Paystack initialize called without secret key configured.');
            return null;
        }

        $response = Http::withToken($secret)
            ->acceptJson()
            ->post(self::API . '/transaction/initialize', [
                'email' => $email,
                'amount' => (int) round($amount * 100), // smallest unit (kobo / cents)
                'currency' => strtoupper($currency),
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

        if (! $response->successful() || ! ($response->json('status') === true)) {
            Log::error('Paystack initialize failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json('data');
    }

    /**
     * Verify a transaction reference. Returns the full data block on success.
     */
    public function verify(string $reference): ?array
    {
        $secret = setting('paystack.secret_key');
        if (! $secret) {
            return null;
        }

        $response = Http::withToken($secret)
            ->acceptJson()
            ->get(self::API . '/transaction/verify/' . urlencode($reference));

        if (! $response->successful() || ! ($response->json('status') === true)) {
            Log::error('Paystack verify failed', [
                'reference' => $reference,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json('data');

        // Paystack reports per-transaction status separate from the API status flag.
        if (($data['status'] ?? null) !== 'success') {
            return null;
        }

        return $data;
    }
}
