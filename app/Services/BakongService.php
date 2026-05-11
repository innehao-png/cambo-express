<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BakongService
{
    protected $baseUrl;
    protected $token;
    protected $accountUsername;
    protected $accountName;

    public function __construct()
    {
        $this->baseUrl = config('services.bakong.base_url');
        $this->token = config('services.bakong.api_token');
        $this->accountUsername = config('services.bakong.account_username');
        $this->accountName = config('services.bakong.account_name');
    }

    /**
     * Generate KHQR String
     */
    public function generateQR($order)
    {
        $payload = [
            "accountInformation" => $this->accountUsername,
            "merchantName"       => $this->accountName,
            "currency"           => "USD", // or KHR
            "amount"             => $order->total_amount,
            "billNumber"         => "ORDER-" . $order->id,
            "storeLabel"         => "Ecommerce",
            "terminalLabel"      => "Online",
        ];

        $response = Http::withToken($this->token)
            // ->withOptions([
            //     'verify' => false,
            // ])
            ->post($this->baseUrl . '/v1/generateKHQR', $payload);

        if ($response->successful()) {
            return $response->json()['qr']; // usually returns "qr"
        }

        throw new \Exception('Failed to generate KHQR: ' . $response->body());
    }

    /**
     * Check Transaction Status
     */
    public function checkTransaction($md5)
    {
        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/v1/check_transaction_by_md5', [
                "md5" => $md5
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to check transaction: ' . $response->body());
    }
}
