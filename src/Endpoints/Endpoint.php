<?php

namespace RezKit\Tours\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use RezKit\Tours\Client;
use RezKit\Tours\Requests\PaginationQuery;
use RezKit\Tours\Responses\Paginated;
use Symfony\Component\Serializer\Serializer;

abstract class Endpoint
{
    protected Serializer $serializer;
    protected GuzzleClient $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
        $this->serializer = Client::createSerializer();
    }
}
