<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowService
{
    protected $apiKey;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.flow.api_key');
        $this->secretKey = config('services.flow.secret_key');

        if (empty($this->apiKey) || empty($this->secretKey)) {
            Log::error('Flow Configuration Error: Missing API Key or Secret Key');
            throw new \Exception('Flow API Key or Secret Key is not configured.');
        }

        $isProduction = config('services.flow.env') === 'production';
        $this->baseUrl = $isProduction
            ? 'https://www.flow.cl/api'
            : 'https://sandbox.flow.cl/api';
    }

    /**
     * Generate HMAC SHA256 signature for Flow API params.
     */
    public function sign(array $params): string
    {
        ksort($params); // Sort alphabetically by key

        $toSign = "";
        foreach ($params as $key => $value) {
            $toSign .= $key . $value;
        }

        return hash_hmac('sha256', $toSign, $this->secretKey);
    }

    /**
     * Create a payment order on Flow.
     * @param array $data ['commerceOrder', 'subject', 'currency', 'amount', 'email', 'urlConfirmation', 'urlReturn']
     * @return array Response from Flow
     */
    public function createPayment(array $data)
    {
        $params = array_merge([
            'apiKey' => $this->apiKey,
        ], $data);

        $params['s'] = $this->sign($params);

        $url = $this->baseUrl . '/payment/create';

        try {
            // Debug Log
            $debugParams = $params;
            $debugParams['apiKey'] = 'MASKED'; // Mask API Key
            Log::info('Flow Payment Request', ['url' => $url, 'params' => $debugParams]);

            $response = Http::asForm()->post($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flow Payment Creation Error', ['body' => $response->body()]);
            throw new \Exception('Error creating payment: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Flow Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get payment status using the token returned by Flow.
     * @param string $token
     * @return array
     */
    public function getStatus(string $token)
    {
        $params = [
            'apiKey' => $this->apiKey,
            'token' => $token
        ];

        $params['s'] = $this->sign($params);

        $url = $this->baseUrl . '/payment/getStatus';

        try {
            $response = Http::get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flow Get Status Error', ['body' => $response->body()]);
            throw new \Exception('Error getting status: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Flow Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
