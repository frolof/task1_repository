<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Service\TransactionProcessor;
use App\Service\BinListServiceInterface;
use App\Service\ExchangeRateServiceInterface;
use App\Exception\TransactionProcessorException;
use App\Exception\BinListException;
use App\Exception\ExchangeRateNotFoundException;

class TransactionProcessorTest extends TestCase
{
    public function testProcess()
    {
        $binListService = $this->createMock(BinListServiceInterface::class);
        $binListService->method('getBinData')->willReturn(['country' => ['alpha2' => 'DE']]);
        $binListService->method('isEu')->willReturn(true);

        $exchangeRateService = $this->createMock(ExchangeRateServiceInterface::class);
        $exchangeRateService->method('getExchangeRate')->willReturn(1.1);

        $transactionProcessor = new TransactionProcessor($binListService, $exchangeRateService);
        $result = $transactionProcessor->process('{"bin":"45717360","amount":"100.00","currency":"EUR"}');

        $this->assertEquals('1.00', $result);
    }

    public function testProcessThrowsException()
    {
        $this->expectException(TransactionProcessorException::class);

        $binListService = $this->createMock(BinListServiceInterface::class);
        $binListService->method('getBinData')->willReturn(['country' => ['alpha2' => 'DE']]);
        $binListService->method('isEu')->willReturn(true);

        $exchangeRateService = $this->createMock(ExchangeRateServiceInterface::class);
        $exchangeRateService->method('getExchangeRate')->willThrowException(new ExchangeRateNotFoundException());

        $transactionProcessor = new TransactionProcessor($binListService, $exchangeRateService);
        $transactionProcessor->process('{"bin":"45717360","amount":"100.00","currency":"EUR"}');
    }

    public function testProcessInvalidBinDataThrowsException()
    {
        $this->expectException(BinListException::class);

        $binListService = $this->createMock(BinListServiceInterface::class);
        $binListService->method('getBinData')->willReturn([]);
        $binListService->method('isEu')->willReturn(true);

        $exchangeRateService = $this->createMock(ExchangeRateServiceInterface::class);
        $exchangeRateService->method('getExchangeRate')->willReturn(1.1);

        $transactionProcessor = new TransactionProcessor($binListService, $exchangeRateService);
        $transactionProcessor->process('{"bin":"45717360","amount":"100.00","currency":"EUR"}');
    }
}
