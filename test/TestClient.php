<?php

namespace Westy92\HolidayEventApi\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

final class TestClient extends \Westy92\HolidayEventApi\Client
{
    private HandlerStack $handler;
    protected function clientBuilder(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client([
            'base_uri' => 'https://api.apilayer.com/checkiday/',
            'handler' => $this->handler,
        ]);
    }

    public function __construct(string $apiKey, MockHandler $mock)
    {
        $this->handler = HandlerStack::create($mock);
        parent::__construct($apiKey);
    }
}
