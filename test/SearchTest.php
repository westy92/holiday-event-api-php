<?php

namespace Westy92\HolidayEventApi\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Westy92\HolidayEventApi\Model\EventSummary;

require_once 'TestClient.php';

$searchDefaultJson = file_get_contents(__DIR__ . '/search-default.json');
$searchParametersJson = file_get_contents(__DIR__ . '/search-parameters.json');

final class SearchTest extends TestCase
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

    public function testSearchWithDefaultParameters()
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'false',
                    'query' => 'zucchini',
                ], $query);
                global $searchDefaultJson;
                return new Response(200, [], $searchDefaultJson);
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->search('zucchini');
        $this->assertEquals(false, $result->adult);
        $this->assertEquals(3, count($result->events));
        $expected = new EventSummary();
        $expected->id = 'cc81cbd8730098456f85f69798cbc867';
        $expected->name = 'National Zucchini Bread Day';
        $expected->url = 'https://www.checkiday.com/cc81cbd8730098456f85f69798cbc867/national-zucchini-bread-day';
        $this->assertEquals($expected, $result->events[0]);
    }

    public function testSearchWithSetParameters()
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'true',
                    'query' => 'porch day',
                ], $query);
                global $searchParametersJson;
                return new Response(200, [], $searchParametersJson);
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->search('porch day', true);
        $this->assertEquals(true, $result->adult);
        $this->assertEquals(1, count($result->events));
        $expected = new EventSummary();
        $expected->id = '61363236f06e4eb8e4e14e5925c2503d';
        $expected->name = "Sneak Some Zucchini Onto Your Neighbor's Porch Day";
        $expected->url = 'https://www.checkiday.com/61363236f06e4eb8e4e14e5925c2503d/sneak-some-zucchini-onto-your-neighbors-porch-day';
        $this->assertEquals($expected, $result->events[0]);
    }
}
