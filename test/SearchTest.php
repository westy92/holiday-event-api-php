<?php

namespace Westy92\HolidayEventApi\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Westy92\HolidayEventApi\Model\EventSummary;

require_once 'TestClient.php';
require_once 'TestJson.php';

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

    public function testSearchWithDefaultParameters(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'false',
                    'query' => 'zucchini',
                ], $query);
                return new Response(200, [], TestJson::searchDefaultJson());
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->search('zucchini');
        $this->assertEquals(false, $result->adult);
        $this->assertEquals(3, count($result->events));
        $expected = new EventSummary(
            id: 'cc81cbd8730098456f85f69798cbc867',
            name: 'National Zucchini Bread Day',
            url: 'https://www.checkiday.com/cc81cbd8730098456f85f69798cbc867/national-zucchini-bread-day',
        );
        $this->assertEquals($expected, $result->events[0]);
    }

    public function testSearchWithSetParameters(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'true',
                    'query' => 'porch day',
                ], $query);
                return new Response(200, [], TestJson::searchParametersJson());
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $result = $client->search('porch day', true);
        $this->assertEquals(true, $result->adult);
        $this->assertEquals(1, count($result->events));
        $expected = new EventSummary(
            id: '61363236f06e4eb8e4e14e5925c2503d',
            name: "Sneak Some Zucchini Onto Your Neighbor's Porch Day",
            url: 'https://www.checkiday.com/61363236f06e4eb8e4e14e5925c2503d/sneak-some-zucchini-onto-your-neighbors-porch-day',
        );
        $this->assertEquals($expected, $result->events[0]);
    }

    public function testSearchQueryTooShort(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'false',
                    'query' => 'a',
                ], $query);
                
                return new Response(
                    400,
                    [],
                    json_encode(['error' => 'Please enter a longer search term.']),
                );
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Please enter a longer search term.');
        $client->search('a');
    }

    public function testSearchTooManyResults(): void
    {
        $this->mock->append(
            function (\GuzzleHttp\Psr7\Request $request) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $this->assertEquals([
                    'adult' => 'false',
                    'query' => 'day',
                ], $query);
                
                return new Response(
                    400,
                    [],
                    json_encode(['error' => 'Too many results returned. Please refine your query.']),
                );
            },
        );
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Too many results returned. Please refine your query.');
        $client->search('day');
    }

    public function testSearchMissingParameters(): void
    {
        $client = new TestClient('abc123', $this->mock);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Search query is required.');
        $client->search('');
    }
}
