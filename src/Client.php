<?php

namespace Westy92\HolidayEventApi;

final class Client
{
    private $client;
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException("Please provide a valid API key. Get one at https://apilayer.com/marketplace/checkiday-api#pricing.");
        }
        $this->apiKey = $apiKey;
        $this->client = new \GuzzleHttp\Client();
    }

    public function getEvents()
    {
        return request('events');
    }

    private function request(string $path): void
    {

    }
}
