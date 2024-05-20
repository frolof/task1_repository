<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Service\BinListService;
use App\Exception\BinListException;

class BinListServiceTest extends TestCase
{
    public function testGetBinData()
    {
        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], json_encode(['country' => ['alpha2' => 'DE']])));

        $binListService = new BinListService($client);
        $binData = $binListService->getBinData('45717360');

        $this->assertEquals('DE', $binData['country']['alpha2']);
    }

    public function testGetBinDataThrowsException()
    {
        $this->expectException(BinListException::class);

        $client = $this->createMock(Client::class);
        $client->method('get')->willReturn(new Response(200, [], json_encode([])));

        $binListService = new BinListService($client);
        $binListService->getBinData('45717360');
    }

    public function testIsEu()
    {
        $client = $this->createMock(Client::class);
        $binListService = new BinListService($client);

        $this->assertTrue($binListService->isEu('DE'));
        $this->assertFalse($binListService->isEu('US'));
    }
}
