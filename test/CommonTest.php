<?php

namespace Westy92\HolidayEventApi\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

require_once 'TestClient.php';

final class CommonTest extends TestCase
{
    protected MockHandler $mock;

    protected function setUp(): void
    {
        $this->mock = new MockHandler([]);
    }

    protected function tearDown(): void
    {
        $this->assertEquals(0, $this->mock->count());
    }

    public function testSendsApiKey()
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals(['abc123'], $request->getHeader('apikey'));
                return new Response(200, [], '{}');
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $client->getEvents();
    }

    public function testSendsUserAgent()
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals(['HolidayApiPHP/1.0.0'], $request->getHeader('user-agent'));
                return new Response(200, [], '{}');
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $client->getEvents();
    }

    public function testSendsPlatformVersion()
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals([phpversion()], $request->getHeader('x-platform-version'));
                return new Response(200, [], '{}');
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $client->getEvents();
    }

    public function testPassesAlongError()
    {
        $this->mock->append(
            new Response(
                401,
                [],
                json_encode(['error' => 'MyError!']),
            ),
        );
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('MyError!');
        $client->getEvents();
    }

    public function testServerError500()
    {
        $this->mock->append(new Response(500));
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('500 Internal Server Error');
        $client->getEvents();
    }

    public function testServerErrorUnknown()
    {
        $this->mock->append(new Response(599));
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('599 ');
        $client->getEvents();
    }

    public function testServerError()
    {
        $this->mock->append(
            new RequestException('derp', new Request('GET', 'test')),
        );
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('derp');
        $client->getEvents();
    }

    public function testServerErrorMalformedResponse()
    {
        $this->mock->append(new Response(200, [], '{'));
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to parse response.');
        $client->getEvents();
    }

    public function testFollowsRedirects()
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals('https://api.apilayer.com/checkiday/events?adult=false', $request->getUri());
                return new Response(302, ['Location' => 'https://www.google.com']);
            },
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals('https://www.google.com', $request->getUri());
                return new Response(200, [], '{}');
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $client->getEvents();
    }

    public function testReportsRateLimits()
    {
        $this->mock->append(
            new Response(200, [
                'X-RateLimit-Remaining-Month' => '123',
                'X-RateLimit-Limit-Month' => '456',
            ], '{}'),
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->getEvents();
        $this->assertEquals(123, $result['rateLimit']['remainingMonth']);
        $this->assertEquals(456, $result['rateLimit']['limitMonth']);
    }
}
