<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected $apiUrl = 'https://api.exchangerate.host/latest';

    /**
     * Get latest exchange rates with a given base currency
     *
     * @param string $base
     * @return array ['base'=>'USD','date'=>'2025-11-11','rates'=>['EUR'=>0.92,...]]
     */
    public function getLatestRates(string $base = 'USD'): array
    {
        $cacheKey = "exchange_rates_{$base}_" . now()->toDateString();

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($base) {
            try {
                $response = Http::timeout(10)->get($this->apiUrl, [
                    'base' => $base, // sets ?base=USD
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['rates']) && is_array($data['rates'])) {
                        return $data;
                    }
                    Log::warning('ExchangeRateService: API returned empty rates', $data);
                } else {
                    Log::warning('ExchangeRateService: API non-success status', ['status' => $response->status()]);
                }
            } catch (\Throwable $e) {
                Log::error('ExchangeRateService: Exception fetching rates', ['error' => $e->getMessage()]);
            }

            // Fallback rates in case API fails
            Log::warning('ExchangeRateService: Using fallback exchange rates');
            return [
                'base' => $base,
                'date' => now()->toDateString(),
                'rates' => [
                    'USD' => 1,
                    'EUR' => 0.92,
                    'AUD' => 1.45,
                    'CAD' => 1.35,
                    'HKD' => 7.85,
                    'INR' => 83.5,
                    'CHF' => 0.88,
                    'JPY' => 145.0,
                ],
            ];
        });
    }

    /**
     * Get rate for a specific currency
     */
    public function getRate(string $currency, string $base = 'USD'): float
    {
        $ratesData = $this->getLatestRates($base);
        return $ratesData['rates'][$currency] ?? 0.0;
    }
}

