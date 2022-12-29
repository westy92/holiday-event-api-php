<?php

namespace Westy92\HolidayEventApi;

class Client
{
    private $client;
    private string $apiKey;
    public const VERSION = '1.0.0';

    protected function clientBuilder(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client([
            'base_uri' => 'https://api.apilayer.com/checkiday/',
        ]);
    }

    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException("Please provide a valid API key. Get one at https://apilayer.com/marketplace/checkiday-api#pricing.");
        }
        $this->apiKey = $apiKey;
        $this->client = $this->clientBuilder();
    }

    public function getEvents(?string $date = null, bool $adult = false, ?string $timezone = null) // TODO return type
    {
        $params = ['adult' => var_export($adult, true)];
        if ($date != null) {
            $params["date"] = $date;
        }
        if ($timezone != null) {
            $params["timezone"] = $timezone;
        }
        return $this->request('events', $params);
    }

    private function request(string $path, $query): void
    {
        $headers = [
            'apikey' => $this->apiKey,
            'User-Agent' => 'HolidayApiPHP/' . $this::VERSION,
            'X-Platform-Version' => phpversion(),
        ];
        $request = $this->client->get($path, [
            'query' => $query,
            'headers' => $headers,
        ]);
    }
}
