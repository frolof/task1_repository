<?php

namespace App\Service;

interface ExchangeRateServiceInterface
{
    public function getExchangeRate(string $currency): float;
}
