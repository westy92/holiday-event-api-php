<?php

namespace Westy92\HolidayEventApi\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Westy92\HolidayEventApi\Model\EventSummary;

require_once 'TestClient.php';

$getEventsDefaultJson = file_get_contents(__DIR__ . '/getEvents-default.json');
$getEventsParametersJson = file_get_contents(__DIR__ . '/getEvents-parameters.json');

final class GetEventsTest extends TestCase
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

    public function testGetEventsWithDefaultParameters(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $this->assertEquals('https://api.apilayer.com/checkiday/events?adult=false', $request->getUri());
                global $getEventsDefaultJson;
                return new Response(200, [], $getEventsDefaultJson);
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->getEvents();
        $this->assertEquals(false, $result->adult);
        $this->assertEquals('America/Chicago', $result->timezone);
        $this->assertEquals(2, count($result->events));
        $this->assertEquals(1, count($result->multidayStarting ?? []));
        $this->assertEquals(2, count($result->multidayOngoing ?? []));
        $expected = new EventSummary(
            id: 'b80630ae75c35f34c0526173dd999cfc',
            name: 'Cinco de Mayo',
            url: 'https://www.checkiday.com/b80630ae75c35f34c0526173dd999cfc/cinco-de-mayo',
        );
        $this->assertEquals($expected, $result->events[0]);
    }

    public function testGetEventsWithSetParameters(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'true',
                    'date' => '7/16/1992',
                    'timezone' => 'America/New_York',
                ], $query);
                global $getEventsParametersJson;
                return new Response(200, [], $getEventsParametersJson);
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->getEvents('7/16/1992', true, 'America/New_York');
        $this->assertEquals(true, $result->adult);
        $this->assertEquals('America/New_York', $result->timezone);
        $this->assertEquals(2, count($result->events));
        $this->assertEquals(0, count($result->multidayStarting ?? []));
        $this->assertEquals(1, count($result->multidayOngoing ?? []));
        $expected = new EventSummary(
            id: '6ebb6fd5e483de2fde33969a6c398472',
            name: 'Get to Know Your Customers Day',
            url: 'https://www.checkiday.com/6ebb6fd5e483de2fde33969a6c398472/get-to-know-your-customers-day',
        );
        $this->assertEquals($expected, $result->events[0]);
    }
}
