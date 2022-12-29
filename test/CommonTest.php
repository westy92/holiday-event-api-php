<?php

namespace Westy92\HolidayEventApi\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

require_once 'TestClient.php';

final class CommonTest extends TestCase
{
    public function testSendsApiKey()
    {
        $mock = new MockHandler([
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertContains('abc123', $request->getHeader('apikey'));
                return new Response();
            },
        ]);
        $client = new TestClient('abc123', $mock);
        $client->getEvents();
        $this->assertEquals(0, $mock->count());
    }

    public function testSendsUserAgent()
    {
        $mock = new MockHandler([
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals('HolidayApiPHP/1.0.0', $request->getHeader('user-agent')[0]);
                return new Response();
            },
        ]);
        $client = new TestClient('abc123', $mock);
        $client->getEvents();
        $this->assertEquals(0, $mock->count());
    }

    public function testSendsPlatformVersion()
    {
        $mock = new MockHandler([
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals(phpversion(), $request->getHeader('x-platform-version')[0]);
                return new Response();
            },
        ]);
        $client = new TestClient('abc123', $mock);
        $client->getEvents();
        $this->assertEquals(0, $mock->count());
    }
}
