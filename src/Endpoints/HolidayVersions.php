<?php

namespace RezKit\Tours\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use RezKit\Tours\Models\HolidayVersion;
use RezKit\Tours\Requests;
use RezKit\Tours\Requests\PaginationQuery;
use RezKit\Tours\Responses\Paginated;
use RezKit\Tours\Responses\PaginatedVersions;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class HolidayVersions extends Endpoint
{
    private string $holidayId;

    public function __construct(GuzzleClient $client, string $holidayId)
    {
        parent::__construct($client);
        $this->holidayId = $holidayId;
    }

    /**
     * List holiday versions
     *
     * @param PaginationQuery|null $params
     * @return Paginated<HolidayVersion> Holiday Versions
     * @throws ExceptionInterface
     * @throws GuzzleException
     */
    public function list(?Requests\PaginationQuery $params = null): Paginated
    {

        $params ??= new Requests\PaginationQuery();

        $path = '/holidays/' . $this->holidayId . '/versions';
        $rsp = $this->client->get($path, ['query' => $this->serializer->normalize($params)]);

        $paginator = new PaginatedVersions($this->client, $this->serializer);

        $body = $rsp->getBody()->getContents();

        $this->serializer->deserialize($body, PaginatedVersions::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $paginator
        ]);

        return $paginator;
    }

    /**
     * Get a specific holiday version
     *
     * @param string $id
     * @return HolidayVersion
     * @throws GuzzleException
     */
    public function find(string $id): HolidayVersion
    {
        $path = '/holidays/' . $this->holidayId . '/versions/' . $id;
        $rsp = $this->client->get($path);

        /** @var HolidayVersion $version */
        $version = $this->serializer->deserialize($rsp->getBody()->getContents(), HolidayVersion::class, 'json');

        return $version;
    }

    /**
     * Create a new Holiday Version
     *
     * @param Requests\CreateHolidayVersion $params
     * @return HolidayVersion
     * @throws GuzzleException
     */
    public function create(Requests\CreateHolidayVersion $params): HolidayVersion
    {
        $path = '/holidays/' . $this->holidayId . '/versions';

        $rsp = $this->client->post($path, [
            RequestOptions::BODY => $this->serializer->serialize($params, 'json'),
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = $rsp->getBody()->getContents();

        /** @var HolidayVersion $version */
        $version = $this->serializer->deserialize($body, HolidayVersion::class, 'json');

        return $version;
    }

    /**
     * Delete a Holiday Version
     *
     * @param string $id
     * @return void
     * @throws GuzzleException
     */
    public function delete(string $id): void
    {
        $path = '/holidays/' . $this->holidayId . '/versions/' . $id;

        $this->client->delete($path);
    }

    /**
     * Restore a deleted Holiday Version
     *
     * @param string $id
     * @return HolidayVersion
     * @throws GuzzleException
     */
    public function restore(string $id): HolidayVersion
    {
        $path = '/holidays/' . $this->holidayId . '/versions/' . $id . '/restore';
        $rsp = $this->client->put($path);

        $body = $rsp->getBody()->getContents();

        /** @var HolidayVersion $version */
        $version = $this->serializer->deserialize($body, HolidayVersion::class, 'json');

        return $version;
    }
}
