<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ABAService
{
    protected string $apiUrl;
    protected string $merchantId;
    protected string $publicKey;

    protected string $rsa_public_key;

    public function __construct()
    {
        $this->apiUrl     = config('services.aba.api_url');
        $this->merchantId = config('services.aba.merchant_id');
        $this->publicKey  = config('services.aba.public_key');
        $this->rsa_public_key = str_replace('\n', '\n', config('services.aba.rsa_public_key'));
    }

    // ================ \\
    // Helper Functions \\
    // ================ \\

    protected function buildPayload(array $data): array
    {
        return [
            'req_time'       => now()->utc()->format('YmdHis'),
            'merchant_id'    => $this->merchantId,
            'tran_id'        => $data['tran_id'],
            'firstname'      => $data['firstname'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'amount'         => number_format((float) $data['amount'], 2, '.', ''),
            'currency'       => $data['currency'] ?? 'USD',
            'type'           => 'purchase',
            'payment_option' => $data['payment_option'] ?? 'abapay_khqr',
            'lifetime'       => (string) ($data['lifetime'] ?? '3'),
        ];

        // Optional customer fields
        // foreach (['firstname', 'lastname', 'email', 'phone'] as $field) {
        //     if (! empty($data[$field])) {
        //         $payload[$field] = $data[$field];
        //     }
        // }

        // // items
        // if (! empty($data['items'])) {
        //     $payload['items'] = is_array($data['items'])
        //         ? base64_encode(json_encode($data['items']))
        //         : $data['items'];
        // }

        // // Optional extras
        // if (! empty($data['custom_fields'])) {
        //     $payload['custom_fields'] = is_array($data['custom_fields'])
        //         ? base64_encode(json_encode($data['custom_fields']))
        //         : $data['custom_fields'];
        // }

        // if (! empty($data['return_params'])) {
        //     $payload['return_params'] = is_array($data['return_params'])
        //         ? json_encode($data['return_params'])
        //         : $data['return_params'];
        // }

        // if (! empty($data['return_deeplink'])) {
        //     $payload['return_deeplink'] = is_array($data['return_deeplink'])
        //         ? base64_encode(json_encode($data['return_deeplink']))
        //         : $data['return_deeplink'];
        // }

        // return $payload;
    }

    protected function buildHashString(array $payload): string
    {
        $fields = [
            'req_time',
            'merchant_id',
            'tran_id',
            'amount',
            'items',
            'shipping',
            'firstname',        // empty string if not in payload
            'lastname',
            'email',
            'phone',
            'type',
            'payment_option',
            'continue_success_url',
            'return_url',
            'cancel_url',
            'return_deeplink',
            'custom_fields',
            'return_params',
            'currency',
            'view_type',
            'payment_gate',
            'skip_success_page',
            'payout',
            'additional_params',
            'lifetime',
            'google_pay_token',
        ];

        return implode('', array_map(
            fn($field) => (string) ($payload[$field] ?? ''),
            $fields
        ));
    }

    protected function generateHash(array $payload): string
    {
        return base64_encode(
            hash_hmac('sha512', $this->buildHashString($payload), $this->publicKey, true)
        );
    }

    protected function getHashString(string $b4hash): string
    {
        return base64_encode(
            hash_hmac('sha512', $b4hash, $this->publicKey, true)
        );
    }

    // ==================== \\
    // End Helper Functions \\
    // ==================== \\

    // ===================== \\
    // API Request Functions \\
    // ===================== \\
    protected function sendRequest(array $payload, string $language = 'en'): array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['language' => $language])
                ->asForm()
                ->post($this->apiUrl, $payload);

            // Log::info('[ABA] Raw response', [
            //     'http_status' => $response->status(),
            //     'body'        => $response->body(),
            // ]);

            $json = $response->json();

            if ($json === null) {
                return ['status' => ['code' => 'PARSE_ERROR', 'message' => $response->body()]];
            }

            if (! isset($json['status'])) {
                Log::warning('[ABA] Unexpected response shape', $json);
                return ['status' => ['code' => 'BAD_SHAPE', 'message' => 'Unexpected response'], 'raw' => $json];
            }

            return $json;
        } catch (\Throwable $e) {
            Log::error('[ABA] Request exception', ['error' => $e->getMessage()]);
            return ['status' => ['code' => 'EXCEPTION', 'message' => $e->getMessage()]];
        }
    }
    public function purchase(array $data, string $language = 'en'): array
    {
        $payload         = $this->buildPayload($data);
        $payload['hash'] = $this->generateHash($payload);

        // Log::info('[ABA] Payload ready', [
        //     'payload'    => array_merge($payload, ['hash' => '***hidden***']),
        //     'hash_input' => $this->buildHashString($payload),
        // ]);

        $response = $this->sendRequest($payload, $language);

        // Include payload fields in response for frontend
        $response['req_time']    = $payload['req_time'] ?? null;
        $response['merchant_id'] = $payload['merchant_id'] ?? null;
        $response['hash']        = $payload['hash'] ?? null;

        return $response;
    }
    public function checkTransaction(string $tran_id): array
    {
        $data = [
            'merchant_id' => $this->merchantId,
            'tran_id'     => $tran_id,
            'req_time'    => now()->utc()->format('YmdHis'),
        ];

        $b4hash  = $data['req_time'] . $data['merchant_id'] . $data['tran_id'];
        $data['hash'] = $this->getHashString($b4hash);
        $response = Http::withoutVerifying()
            ->asMultipart()
            ->post(
                'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2',
                $data
            );

        // dd($response->json());

        return $response->json();
    }
    public function getTransactionDetail(string $tran_id)
    {

        $data = [
            'req_time' => now()->utc()->format('YmdHis'),
            'merchant_id' => $this->merchantId,
            'tran_id' => $tran_id,
        ];

        $b4hash = $data['req_time'] . $data['merchant_id'] . $data['tran_id'];

        $data['hash'] = $this->getHashString($b4hash);

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/transaction-detail', $data);


        return $response->json();
    }
    public function getTransactionsList(array $filters = [])
    {
        $data = [
            'req_time'    => now()->utc()->format('YmdHis'),
            'merchant_id' => $this->merchantId,
            'from_date'   => $filters['from_date'] ?? now('UTC')->startOfDay()->format('Y-m-d H:i:s'),
            'to_date'     => $filters['to_date']   ?? now('UTC')->endOfDay()->format('Y-m-d H:i:s'),
            'from_amount' => $filters['from_amount'] ?? '0.00',
            'to_amount'   => $filters['to_amount']   ?? '0.00',
            'status'      => $filters['status']      ?? 'APPROVED',
            'page'        => (string) ($filters['page']       ?? '1'),
            'pagination'  => (string) ($filters['pagination'] ?? '5'),
        ];

        $b4hash = $data['req_time'] . $data['merchant_id'] . $data['from_date'] . $data['to_date'] .
            $data['from_amount'] . $data['to_amount'] . $data['status'] . $data['page'] . $data['pagination'];

        $data['hash'] = $this->getHashString($b4hash);

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/transaction-list-2', $data);

        return $response->json();
    }
    public function closeTransaction(string $tran_id)
    {
        $data = [
            'req_time' => now()->utc()->format('YmdHis'),
            'merchant_id' => $this->merchantId,
            'tran_id' => $tran_id,
        ];

        $b4hash = $data['req_time'] . $data['merchant_id'] . $data['tran_id'];

        $data['hash'] = $this->getHashString($b4hash);

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/close-transaction', $data);

        return $response->json();
    }
    public function refundTransaction(string $tran_id, float $amount)
    {
        $data_object = json_encode([
            'mc_id' => $this->merchantId,
            'tran_id' => $tran_id,
            'refund_amount' => $amount
        ]);

        $rsa_public_key = $this->rsa_public_key;
        $maxLength = 117;
        $encrypted_output = '';

        $key = openssl_pkey_get_public($rsa_public_key);
        if (!$key) {
            throw new Exception('Invalid RSA public key: ' . openssl_error_string());
        }

        while ($data_object !== '') {
            $chunk = substr($data_object, 0, $maxLength);
            $data_object = substr($data_object, $maxLength);

            if (openssl_public_encrypt($chunk, $encrypted_chunk, $rsa_public_key)) {
                $encrypted_output .= $encrypted_chunk;
            } else {
                throw new Exception('Encryption failed for a data chunk.');
            }
        }

        $merchant_auth = base64_encode($encrypted_output);

        $data = [
            'request_time' => now()->utc()->format('YmdHis'),
            'merchant_id' => $this->merchantId,
            'merchant_auth' => $merchant_auth
        ];

        $b4hash = $data['request_time'] . $data['merchant_id'] . $data['merchant_auth'];

        $data['hash'] = $this->getHashString($b4hash);

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('https://checkout-sandbox.payway.com.kh/api/merchant-portal/merchant-access/online-transaction/refund', $data);

        return $response->json();
    }
    public function exchangeRate()
    {
        $data = [
            'req_time'    => now()->utc()->format('YmdHis'),
            'merchant_id' => $this->merchantId,
        ];

        $b4hash = $data['req_time'] . $data['merchant_id'];

        $data['hash'] = $this->getHashString($b4hash);

        $response = Http::withoutVerifying()
            ->asJson()
            ->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/exchange-rate', $data);

        $json = $response->json();

        // dd($json['status']['code']);

        if (($json['status']['code'] ?? null) !== '00') {
            return $json;
        }

        $xmlString = $json['exchange_rates']['OclResposne']['io_rates'] ?? null;

        if (!$xmlString) {
            return $json;
        }

        $xml = simplexml_load_string($xmlString);

        $rates = [];

        foreach ($xml->rates->rate as $rate) {

            $currency = strtolower((string) $rate);

            $rates[$currency] = [
                'sell' => (string) $rate['sell'],
                'buy' => (string) $rate['buy']
            ];

        }

        return [
            'status'         => $json['status'],
            'date'           => (string) $xml->date,
            'exchange_rates' => $rates,
        ];
    }

    public function generateQR(string $tran_id, float $amount, string $currency)
    {

        $data = [
            'req_time' => now()->utc()->format('YmdHis'),
            'merchant_id'=> $this->merchantId,
            'tran_id' => $tran_id,
            'firstname' => ''
        ];

    }

    // ========================= \\
    // End API Request Functions \\
    // ========================= \\
}
