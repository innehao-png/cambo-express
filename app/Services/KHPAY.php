<?php

namespace App\Services;


class KHPAY
{

    protected string $apiKey;
    protected string $apiUrl;
    protected string $API_KEY;
    protected string $BASE_URL;

    public function __construct()
    {
        $this->apiKey = env('KH_PAYWAY_KEY');
        $this->apiUrl = env('KH_PAYWAY_API_URL');
        $this->API_KEY = env('API_KEY');
        $this->BASE_URL = env('BASE_URL');
    }

    public function createPayment(string $currency, float $amount, ?string $apiKey = null)
    {
        $key = $apiKey ?? $this->apiKey;
        $ch = curl_init($this->apiUrl."/create-payment");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bypass SSL for local dev
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "currency" => $currency,
            "amount" => $amount
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $key,
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['message' => 'CURL Error: ' . $error_msg];
        }

        $result = json_decode($response, true);
        curl_close($ch);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['message' => 'JSON Decode Error: ' . json_last_error_msg(), 'raw_response' => $response];
        }

        return $result;
    }

    public function checkStatusByMd5(string $md5Hash, ?string $apiKey = null)
    {
        $key = $apiKey ?? $this->apiKey;
        $ch = curl_init($this->apiUrl."/check-payment/md5");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "md5_hash" => $md5Hash
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $key,
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['message' => 'CURL Error: ' . $error_msg];
        }

        $result = json_decode($response, true);
        curl_close($ch);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['message' => 'JSON Decode Error: ' . json_last_error_msg(), 'raw_response' => $response];
        }

        return $result;
    }

    public function generateQR(float $amount, ?string $note)
    {
        $ch = curl_init($this->BASE_URL."/qr/generate");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['amount' => $amount, 'note' => $note]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->API_KEY,
                'Content-Type: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['message' => 'CURL Error: ' . $error_msg];
        }

        $data = json_decode($response, true);
        curl_close($ch);

        return $data;
    }
}
