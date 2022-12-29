<?php

namespace Westy92\HolidayEventApi\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Westy92\HolidayEventApi\Model\EventSummary;
use Westy92\HolidayEventApi\Model\Occurrence;

require_once 'TestClient.php';

$getEventInfoDefaultJson = file_get_contents(__DIR__ . '/getEventInfo.json');
$getEventInfoParametersJson = file_get_contents(__DIR__ . '/getEventInfo-parameters.json');

final class GetEventInfoTest extends TestCase
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

    public function testGetEventInfoWithDefaultParameters(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals('https://api.apilayer.com/checkiday/event?id=f90b893ea04939d7456f30c54f68d7b4', $request->getUri());
                global $getEventInfoDefaultJson;
                return new Response(200, [], $getEventInfoDefaultJson);
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->getEventInfo('f90b893ea04939d7456f30c54f68d7b4');
        $this->assertEquals(2, count($result->event->hashtags ?? []));
    }

    public function testGetEventInfoWithSetParameters(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'id' => 'f90b893ea04939d7456f30c54f68d7b4',
                    'start' => '2002',
                    'end' => '2003',
                ], $query);
                global $getEventInfoParametersJson;
                return new Response(200, [], $getEventInfoParametersJson);
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->getEventInfo('f90b893ea04939d7456f30c54f68d7b4', 2002, 2003);
        $this->assertEquals(2, count($result->event->occurrences ?? []));
        $expected = new Occurrence(
            date: '08/08/2002',
            length: 1,
        );
        $this->assertEquals($expected, ($result->event->occurrences ?? [])[0]);
    }

    public function testGetEventInfoInvalidEvent(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals('https://api.apilayer.com/checkiday/event?id=hi', $request->getUri());
                return new Response(
                    404,
                    [],
                    json_encode(['error' => 'Event not found.']),
                );
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Event not found.');
        $client->getEventInfo('hi');
    }

    public function testGetEventInfoMissingId(): void
    {
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event id is required.');
        $client->getEventInfo('');
    }
}
