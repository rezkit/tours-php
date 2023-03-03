<?php

namespace RezKit\Tours\Endpoints;

use GuzzleHttp\RequestOptions;
use RezKit\Tours\Models\Holiday;
use GuzzleHttp\Exception\GuzzleException;
use RezKit\Tours\Requests\ListHolidays;
use RezKit\Tours\Requests\UpdateHolidayRequest;
use RezKit\Tours\Responses\Paginated;
use RezKit\Tours\Responses\PaginatedHolidays;
use RezKit\Tours\Requests;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class Holidays extends Endpoint
{
    /**
     * List holidays, returning paginated, iterable holidays.
     *
     * @param ?ListHolidays $params Query parameters
     * @return Paginated<Holiday> Paginated holidays data
     * @throws GuzzleException
     * @throws ExceptionInterface
     */
    public function list(?ListHolidays $params = null): Paginated
    {
        $params ??= new ListHolidays();

        $rsp = $this->client->request('GET', '/holidays', [
            'query' => $this->serializer->normalize($params),
        ]);

        $body = $rsp->getBody()->getContents();

        $paginator = new PaginatedHolidays($this->client, $this->serializer);

        $this->serializer->deserialize($body, PaginatedHolidays::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $paginator
        ]);

        return $paginator;
    }

    /**
     * Create a new Holiday
     * @param Requests\CreateHoliday $params
     * @return Holiday
     * @throws GuzzleException
     */
    public function create(Requests\CreateHoliday $params): Holiday
    {
        $body = $this->serializer->serialize($params, 'json');

        $rsp = $this->client->request('POST', '/holidays', [
            RequestOptions::BODY => $body,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json'
            ]
        ]);

        /**
         * @var Holiday $holiday
         */
        $holiday = $this->serializer->deserialize($rsp->getBody()->getContents(), Holiday::class, 'json');

        return $holiday;
    }

    /**
     * Find a single holiday by ID.
     *
     * @throws GuzzleException
     */
    public function find(string $id): Holiday
    {
        $rsp = $this->client->request('GET',"/holidays/$id");

        /**
         * @var Holiday $holiday
         */
        $holiday = $this->serializer->deserialize($rsp->getBody()->getContents(), Holiday::class, 'json');

        return $holiday;
    }

    /**
     * Update an existing holiday
     *
     * @throws GuzzleException
     */
    public function update(string $id, UpdateHolidayRequest $request): Holiday
    {
        $rsp = $this->client->request('POST', "/holidays/$id", [
            'json' => $this->serializer->serialize($request, 'json')
        ]);

        /** @var Holiday $holiday */
        $holiday = $this->serializer->deserialize($rsp->getBody()->getContents(), Holiday::class, 'json');

        return $holiday;
    }


    /**
     * Delete a Holiday
     *
     * @param string $id
     * @return void
     * @throws GuzzleException
     */
    public function delete(string $id): void
    {
        $rsp = $this->client->request('DELETE', '/holidays/' . $id);
        return;
    }

    /**
     * Restore a deleted holiday
     *
     * @param string $id
     * @return Holiday
     * @throws GuzzleException
     */
    public function restore(string $id): Holiday
    {
        $rsp = $this->client->request('PUT', '/holidays/' . $id . '/restore');

        $body = $rsp->getBody()->getContents();

        /** @var Holiday $holiday */
        $holiday = $this->serializer->deserialize($body, Holiday::class, 'json');

        return $holiday;
    }

    /**
     * Get Holiday Versions Endpoint
     *
     * @param string $id
     * @return HolidayVersions
     */
    public function versions(string $id): HolidayVersions
    {
        return new HolidayVersions($this->client, $id);
    }
}
