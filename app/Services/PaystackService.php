<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Paystack Payment Gateway Service
 *
 * Handles all Paystack API interactions including payment initialization,
 * webhook verification, and transaction management for Nigerian Naira (NGN) payments.
 *
 * @author JoAla Team
 * @license MIT
 * @version 1.0.0
 *
 * ## Setup
 * Add these to your .env file:
 *   PAYSTACK_PUBLIC_KEY=pk_live_YOUR_PUBLIC_KEY
 *   PAYSTACK_SECRET_KEY=sk_live_YOUR_SECRET_KEY
 *   PAYSTACK_CALLBACK_URL=https://yourdomain.com/checkout/success
 *
 * ## Paystack Dashboard
 * Get your API keys from: https://dashboard.paystack.co/settings/api
 * Use test keys (pk_test_) during development, live keys (pk_live_) for production.
 */
class PaystackService
{
    /** @var string Paystack API base URL */
    private const BASE_URL = 'https://api.paystack.co';

    /** @var string Paystack public key from environment */
    private string $publicKey;

    /** @var string Paystack secret key from environment */
    private string $secretKey;

    /** @var int Request timeout in seconds */
    private int $timeout = 30;

    /**
     * Initialize the Paystack service with API credentials.
     */
    public function __construct()
    {
        $this->publicKey = config('services.paystack.public_key') ?: $this->getFromSettings('paystack_public_key');
        $this->secretKey = config('services.paystack.secret_key') ?: $this->getFromSettings('paystack_secret_key');
    }

    private function getFromSettings(string $key): string
    {
        try {
            $setting = \App\Models\Setting::where('key', $key)->first();
            return $setting ? $setting->value : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Initialize a new payment transaction.
     *
     * Creates a Paystack transaction and returns the authorization URL for
     * redirecting the customer to complete payment.
     *
     * @param string $email Customer's email address
     * @param int    $amount Amount in Kobo (NGN × 100). e.g. 5500000 = ₦55,000
     * @param string $reference Unique transaction reference (max 100 chars)
     * @param string $callbackUrl URL to redirect after payment
     * @param array  $metadata Additional data to store with the transaction
     *
     * @return array{status: bool, message: string, data?: array, reference?: string, url?: string}
     *
     * @example
     * $result = $paystack->initializePayment(
     *     'customer@example.com',
     *     5500000,  // ₦55,000 in kobo
     *     'ORD-20240115-0001',
     *     'https://myshop.com/checkout/success',
     *     ['product' => 'E-commerce Starter Kit', 'order_id' => 5]
     * );
     *
     * if ($result['status']) {
     *     // Redirect customer to $result['url']
     *     header('Location: ' . $result['url']);
     * }
     */
    public function initializePayment(
        string $email,
        int $amount,
        string $reference,
        string $callbackUrl,
        array $metadata = []
    ): array {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout($this->timeout)
                ->post(self::BASE_URL . '/transaction/initialize', [
                    'email' => $email,
                    'amount' => $amount,
                    'reference' => $reference,
                    'callback_url' => $callbackUrl,
                    'metadata' => array_merge($metadata, [
                        'platform' => 'E-Shop Starter Kit v1.0',
                    ]),
                    'channels' => ['card', 'ussd', 'bank_transfer', 'mobile_money'],
                    'currency' => 'NGN',
                    'locale' => 'en-NG',
                ]);

            $body = $response->json();

            if ($body['status']) {
                return [
                    'status' => true,
                    'message' => 'Payment initialized',
                    'data' => $body['data'],
                    'reference' => $body['data']['reference'],
                    'url' => $body['data']['authorization_url'],
                ];
            }

            Log::error('Paystack init failed', [
                'reference' => $reference,
                'response' => $body,
            ]);

            return [
                'status' => false,
                'message' => $body['message'] ?? 'Failed to initialize payment',
            ];
        } catch (Exception $e) {
            Log::error('Paystack exception during init', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Payment service unavailable. Please try again.',
            ];
        }
    }

    /**
     * Verify a Paystack transaction by reference.
     *
     * After payment, call this to confirm the transaction status and amount.
     * Always verify before fulfilling orders.
     *
     * @param string $reference The transaction reference from Paystack
     *
     * @return array{status: bool, message: string, data?: array, amount?: int, currency?: string}
     *
     * @example
     * $verification = $paystack->verifyPayment('ORD-20240115-0001');
     * if ($verification['status'] && $verification['data']['status'] === 'success') {
     *     // Process the order
     *     updateOrderPaymentStatus($reference, 'paid');
     * }
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout($this->timeout)
                ->get(self::BASE_URL . "/transaction/verify/{$reference}");

            $body = $response->json();

            if ($body['status'] && $body['data']['status'] === 'success') {
                return [
                    'status' => true,
                    'message' => 'Payment verified',
                    'data' => [
                        'reference' => $body['data']['reference'],
                        'amount' => (int) $body['data']['amount'],
                        'currency' => $body['data']['currency'],
                        'status' => $body['data']['status'],
                        'customer_email' => $body['data']['customer']['email'],
                        'authorization' => $body['data']['authorization'],
                        'paid_at' => $body['data']['paid_at'],
                    ],
                    'amount' => (int) $body['data']['amount'],
                    'currency' => $body['data']['currency'],
                ];
            }

            $status = $body['data']['status'] ?? 'unknown';
            return [
                'status' => false,
                'message' => "Payment status: {$status}",
                'data' => [
                    'status' => $status,
                    'reference' => $body['data']['reference'] ?? $reference,
                ],
            ];
        } catch (Exception $e) {
            Log::error('Paystack verification exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Could not verify payment. Please contact support.',
            ];
        }
    }

    /**
     * Charge a customer using a saved authorization code.
     *
     * For implementing subscription billing or repeat charges.
     *
     * @param string $email Customer email
     * @param int    $amount Amount in kobo
     * @param string $authorizationCode Authorization code from previous transaction
     * @param string $reference Unique reference for this charge
     * @param array  $metadata Additional metadata
     *
     * @return array{status: bool, message: string, data?: array}
     */
    public function chargeAuthorization(
        string $email,
        int $amount,
        string $authorizationCode,
        string $reference,
        array $metadata = []
    ): array {
        try {
            $response = Http::withToken($this->secretKey)
                ->timeout($this->timeout)
                ->post(self::BASE_URL . '/transaction/charge_authorization', [
                    'email' => $email,
                    'amount' => $amount,
                    'authorization_code' => $authorizationCode,
                    'reference' => $reference,
                    'metadata' => $metadata,
                    'currency' => 'NGN',
                ]);

            $body = $response->json();

            return [
                'status' => $body['status'],
                'message' => $body['message'] ?? '',
                'data' => $body['data'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Paystack charge authorization failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Charge failed. Please try a different payment method.',
            ];
        }
    }

    /**
     * List all transactions with pagination.
     *
     * @param int $perPage Number of results per page (max 100)
     * @param array $filters Optional filters: status, customer, amount range
     *
     * @return array
     */
    public function listTransactions(int $perPage = 20, array $filters = []): array
    {
        try {
            $params = ['perPage' => min($perPage, 100)];

            if (!empty($filters['status'])) {
                $params['status'] = $filters['status'];
            }
            if (!empty($filters['customer'])) {
                $params['customer'] = $filters['customer'];
            }

            $response = Http::withToken($this->secretKey)
                ->timeout($this->timeout)
                ->get(self::BASE_URL . '/transaction', $params);

            return [
                'status' => true,
                'data' => $response->json()['data'] ?? [],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the public key for use in frontend JavaScript.
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Check if Paystack is properly configured.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->publicKey) && !empty($this->secretKey)
            && str_starts_with($this->publicKey, 'pk_');
    }
}