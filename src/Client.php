<?php

namespace Westy92\HolidayEventApi;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class Client
{
    public const VERSION = '1.0.0';

    private \GuzzleHttp\Client $client;
    private array $defaultHeaders;
    private Serializer $serializer;

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

        $this->defaultHeaders = [
            'apikey' => $apiKey,
            'User-Agent' => 'HolidayApiPHP/' . $this::VERSION,
            'X-Platform-Version' => phpversion(),
        ];

        $encoders = [new JsonEncoder()];
        $normalizers = [new PropertyNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->client = $this->clientBuilder();
    }

    public function getEvents(?string $date = null, bool $adult = false, ?string $timezone = null): Model\GetEventsResponse
    {
        $params = ['adult' => var_export($adult, true)];
        if ($date != null) {
            $params["date"] = $date;
        }
        if ($timezone != null) {
            $params["timezone"] = $timezone;
        }
        return $this->request('events', $params, Model\GetEventsResponse::class);
    }

    private function request(string $path, array $query, string $type) // TODO return type
    {
        try {
            $response = $this->client->get($path, [
                'query' => $query,
                'headers' => $this->defaultHeaders,
            ]);

            $result = $this->serializer->deserialize($response->getBody(), $type, 'json');

            $rateLimit = new Model\RateLimit();
            $limit = $response->getHeader("x-ratelimit-limit-month");
            $remaining = $response->getHeader("x-ratelimit-remaining-month");
            $rateLimit->limitMonth = empty($limit) ? 0 : intval($limit[0]);
            $rateLimit->remainingMonth = empty($remaining) ? 0 : intval($remaining[0]);
            $result->rateLimit = $rateLimit;

            return $result;
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $json = json_decode($e->getResponse()->getBody(), true);
                throw new \RuntimeException($json['error'] 
                    ?? $e->getResponse()->getReasonPhrase() 
                    ?? $e->getResponse()->getStatusCode()
                );
            }
        } catch (NotEncodableValueException) {
            throw new \RuntimeException('Unable to parse response.');
        }
    }
}
