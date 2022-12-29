<?php

namespace Westy92\HolidayEventApi;

use Doctrine\Common\Annotations\AnnotationReader;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
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
            throw new \InvalidArgumentException('Please provide a valid API key. Get one at https://apilayer.com/marketplace/checkiday-api#pricing.');
        }

        $this->defaultHeaders = [
            'apikey' => $apiKey,
            'User-Agent' => 'HolidayApiPHP/' . $this::VERSION,
            'X-Platform-Version' => phpversion(),
        ];

        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
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
        return $this->request('search', $params, Model\SearchResponse::class);
    }

    private function request(string $path, array $query, string $type) // TODO return type
    {
        try {
            $response = $this->client->get($path, [
                'query' => $query,
                'headers' => $this->defaultHeaders,
            ]);

            $result = $this->serializer->deserialize($response->getBody(), $type, 'json');

            $limit = $response->getHeader('x-ratelimit-limit-month');
            $remaining = $response->getHeader('x-ratelimit-remaining-month');
            $rateLimit = new Model\RateLimit(
                limitMonth: empty($limit) ? 0 : intval($limit[0]),
                remainingMonth: empty($remaining) ? 0 : intval($remaining[0]),
            );
            $result->rateLimit = $rateLimit;

            return $result;
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $json = json_decode($e->getResponse()->getBody()->__toString(), true);
                throw new \RuntimeException($json['error']
                    ?? $e->getResponse()->getReasonPhrase()
                    ?: $e->getResponse()->getStatusCode()
                );
            }
        } catch (NotEncodableValueException) {
            throw new \RuntimeException('Unable to parse response.');
        }
    }
}
