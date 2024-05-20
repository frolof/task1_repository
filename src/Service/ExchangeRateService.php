<?php

namespace App\Service;

use GuzzleHttp\Client;
use App\Exception\ExchangeRateNotFoundException;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    private const EXCHANGE_RATE_API_URL = 'http://api.exchangeratesapi.io/latest';

    private Client $client;
    private string $accessKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->accessKey = $_ENV['EXCHANGE_RATE_API_ACCESS_KEY'] ?? '';
    }

    public function getExchangeRate(string $currency): float
    {
        // Fetch exchange rates from the API
        $response = $this->client->get(self::EXCHANGE_RATE_API_URL, [
            'query' => ['access_key' => $this->accessKey]
        ]);
        $rates = json_decode($response->getBody(), true)['rates'] ?? [];

        // Check if the requested currency rate is available
        if (!isset($rates[$currency])) {
            throw new ExchangeRateNotFoundException('Exchange rate for currency ' . $currency . ' not found.');
        }

        return $rates[$currency];
    }
}
