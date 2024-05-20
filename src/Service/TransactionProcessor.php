<?php

namespace App\Service;

use App\Exception\ExchangeRateNotFoundException;
use App\Exception\TransactionProcessorException;
use App\Exception\BinListException;

class TransactionProcessor
{
    private const ALPHA2_KEY = 'alpha2';
    private const COUNTRY_KEY = 'country';
    private const BIN_KEY = 'bin';
    private const AMOUNT_KEY = 'amount';
    private const CURRENCY_KEY = 'currency';

    private BinListServiceInterface $binService;
    private ExchangeRateServiceInterface $exchangeRateService;

    public function __construct(BinListServiceInterface $binService, ExchangeRateServiceInterface $exchangeRateService)
    {
        $this->binService = $binService;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function process(string $transaction): string
    {
        $data = json_decode($transaction, true);
        $bin = $data[self::BIN_KEY];
        $amount = (float) $data[self::AMOUNT_KEY];
        $currency = $data[self::CURRENCY_KEY];

        // Get BIN data
        $binData = $this->binService->getBinData($bin);

        // Validate BIN data
        if (!isset($binData[self::COUNTRY_KEY][self::ALPHA2_KEY])) {
            throw new BinListException('Invalid BIN data: ' . self::ALPHA2_KEY . ' key missing');
        }

        // Check if the country is in the EU
        $isEu = $this->binService->isEu($binData[self::COUNTRY_KEY][self::ALPHA2_KEY]);

        try {
            // Get the exchange rate
            $rate = $this->exchangeRateService->getExchangeRate($currency);
        } catch (ExchangeRateNotFoundException $e) {
            throw new TransactionProcessorException('Error processing transaction: ' . $e->getMessage());
        }

        // Convert amount to EUR
        $amountInEur = $currency === 'EUR' ? $amount : $amount / $rate;
        // Calculate the commission
        $commission = $amountInEur * ($isEu ? 0.01 : 0.02);

        // Return the commission rounded up to 2 decimal places
        return number_format(ceil($commission * 100) / 100, 2);
    }
}
