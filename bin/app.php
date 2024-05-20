<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use GuzzleHttp\Client;
use App\Service\TransactionProcessor;
use App\Service\BinListService;
use App\Service\ExchangeRateService;
use App\Exception\TransactionProcessorException;

// Load .env file
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$client = new Client();
$binListService = new BinListService($client);
$exchangeRateService = new ExchangeRateService($client);
$transactionProcessor = new TransactionProcessor($binListService, $exchangeRateService);

$inputFile = $argv[1];
$transactions = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($transactions as $transaction) {
    try {
        echo $transactionProcessor->process($transaction) . "\n";
    } catch (TransactionProcessorException $e) {
        echo 'Error processing transaction: ' . $e->getMessage() . "\n";
    }
}
