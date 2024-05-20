<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Service\ExchangeRateService;
use App\Exception\ExchangeRateNotFoundException;

class ExchangeRateServiceTest extends TestCase
{
    public function testGetExchangeRate()
    {
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], json_encode(['rates' => ['USD' => 1.1]])));

        $exchangeRateService = new ExchangeRateService($client);
        $rate = $exchangeRateService->getExchangeRate('USD');

        $this->assertEquals(1.1, $rate);
    }

    public function testGetExchangeRateThrowsException()
    {
        $this->expectException(ExchangeRateNotFoundException::class);

        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], json_encode(['rates' => []])));

        $exchangeRateService = new ExchangeRateService($client);
        $exchangeRateService->getExchangeRate('USD');
    }
}
