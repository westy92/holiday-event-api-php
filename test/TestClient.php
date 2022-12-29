<?php

namespace Westy92\HolidayEventApi\Tests;

final class TestClient extends \Westy92\HolidayEventApi\Client
{
    private $handler;
    protected function clientBuilder(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client([
            'base_uri' => 'https://api.apilayer.com/checkiday/',
            'handler' => $this->handler,
        ]);
    }

    public function __construct(string $apiKey, $mock)
    {
        $this->handler = \GuzzleHttp\HandlerStack::create($mock);
        parent::__construct($apiKey);
    }
}
