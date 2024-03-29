<?php

namespace Westy92\HolidayEventApi;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class Client
{
    public static string $version = '1.0.1';

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
            throw new \InvalidArgumentException('Please provide a valid API key. Get one at https://apilayer.com/marketplace/checkiday-api#pricing.');
        }

        $this->defaultHeaders = [
            'apikey' => $apiKey,
            'User-Agent' => 'HolidayApiPHP/' . self::$version,
            'X-Platform-Version' => phpversion(),
        ];

        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $typeExtractor = new PropertyInfoExtractor(typeExtractors: [new PhpDocExtractor()]);
        $normalizers = [
            new ArrayDenormalizer(),
            new PropertyNormalizer($classMetadataFactory, $metadataAwareNameConverter, $typeExtractor),
        ];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->client = $this->clientBuilder();
    }

    /**
     * Gets the Events for the provided Date
     */
    public function getEvents(?string $date = null, bool $adult = false, ?string $timezone = null): Model\GetEventsResponse
    {
        $params = ['adult' => var_export($adult, true)];
        if ($date != null) {
            $params['date'] = $date;
        }
        if ($timezone != null) {
            $params['timezone'] = $timezone;
        }
        /**
         * @var Model\GetEventsResponse
         */
        return $this->request('events', $params, Model\GetEventsResponse::class);
    }

    /**
     * Gets the Event Info for the provided Event
     */
    public function getEventInfo(string $id, ?int $start = null, ?int $end = null): Model\GetEventInfoResponse {
        if (empty($id)) {
            throw new \InvalidArgumentException('Event id is required.');
        }
        $params = [
            'id' => $id,
        ];
        if ($start != null) {
            $params['start'] = strval($start);
        }
        if ($end != null) {
            $params['end'] = strval($end);
        }
        /**
         * @var Model\GetEventInfoResponse
         */
        return $this->request('event', $params, Model\GetEventInfoResponse::class);
    }

    /**
     * Searches for Events with the given criteria
     */
    public function search(string $query, bool $adult = false): Model\SearchResponse {
        if (empty($query)) {
            throw new \InvalidArgumentException('Search query is required.');
        }
        $params = [
            'query' => $query,
            'adult' => var_export($adult, true),
        ];
        /**
         * @var Model\SearchResponse
         */
        return $this->request('search', $params, Model\SearchResponse::class);
    }

    private function request(string $path, array $query, string $type): Model\StandardResponse
    {
        try {
            $response = $this->client->get($path, [
                'query' => $query,
                'headers' => $this->defaultHeaders,
            ]);

            /**
             * @var Model\StandardResponse
             */
            $result = $this->serializer->deserialize($response->getBody(), $type, 'json');

            $limit = $response->getHeader('x-ratelimit-limit-month');
            $remaining = $response->getHeader('x-ratelimit-remaining-month');
            $result->rateLimit = new Model\RateLimit(
                limitMonth: empty($limit) ? 0 : intval($limit[0]),
                remainingMonth: empty($remaining) ? 0 : intval($remaining[0]),
            );

            return $result;
        } catch (ClientException $e) {
            /**
             * @var mixed
             */
            $json = json_decode($e->getResponse()->getBody()->__toString(), true);
            /**
             * @var ?string
             */
            $error = is_array($json) && isset($json['error']) && is_string($json['error']) ? $json['error'] : null;
            throw new \RuntimeException($error
                ?? $e->getResponse()->getReasonPhrase()
                ?: strval($e->getResponse()->getStatusCode())
            );
        } catch (NotEncodableValueException) {
            throw new \RuntimeException('Unable to parse response.');
        }
    }
}
