<?php

namespace App\Service;

use GuzzleHttp\Client;
use App\Exception\BinListException;

class BinListService implements BinListServiceInterface
{
    private const BIN_LIST_API_URL = 'https://lookup.binlist.net/';
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR',
        'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
    ];

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getBinData(string $bin): array
    {
        $response = $this->client->get(self::BIN_LIST_API_URL . $bin);
        $data = json_decode($response->getBody(), true);

        if (!isset($data['country']['alpha2'])) {
            throw new BinListException('Invalid BIN data');
        }

        return $data;
    }

    public function isEu(string $countryCode): bool
    {
        return in_array($countryCode, self::EU_COUNTRIES, true);
    }
}
