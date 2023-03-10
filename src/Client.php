<?php

declare(strict_types=1);

namespace RezKit\Tours;

use Doctrine\Common\Annotations\AnnotationReader;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use RezKit\Tours\Middleware\ApiToken;

/**
 * API Client for RezKit Tour Manager
 *
 * Provides an interface to REST and GraphQL APIs
 */
class Client
{
    private GuzzleClient $client;

    /**
     * URL for the API host
     */
    public const API_HOST = 'https://api.tours.rezkit.app';

    /**
     * Create a new API Client instance
     *
     * @param string $apiKey RezKit Tours API Key
     * @param array<string, mixed> $config Additional GuzzlePHP configuration options
     * @return Client RezKit Tours API Client
     */
    public function __construct(string $apiKey, array $config = [])
    {
        if (!array_key_exists('base_uri', $config)) {
            $config['base_uri'] = static::API_HOST;
        }

        if (!array_key_exists('headers', $config)) {
            $config['headers'] = [];
        }

        if (array_key_exists('handler', $config)) {
            /** @var HandlerStack $stack */
            $stack = $config['handler'];
        } else {
            $stack = HandlerStack::create();
            $stack->setHandler(new CurlHandler());
        }

        $stack->push(ApiToken::create($apiKey));

        $config['handler'] = $stack;

        $this->client = new GuzzleClient($config);
    }

    /**
     * Manage Holidays and their dependants
     *
     * @return Endpoints\Holidays Holiday management handler
     */
    public function holidays(): Endpoints\Holidays
    {
        return new Endpoints\Holidays($this->client);
    }

    /**
     * Create a pre-configured data serialization handler
     *
     * @internal
     * @return Serializer Data serialization handler
     */
    public static function createSerializer(): Serializer
    {
        return new Serializer(
            [
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new ObjectNormalizer(
                    new ClassMetadataFactory(
                        new AnnotationLoader(new AnnotationReader()
                        )
                    ),
                    new CamelCaseToSnakeCaseNameConverter(),
                    null,
                    new PhpDocExtractor()
                ),
            ],
            [new JsonDecode(), new JsonEncode()]
        );
    }
}
