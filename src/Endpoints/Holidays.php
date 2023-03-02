<?php

namespace RezKit\Tours\Endpoints;

use RezKit\Tours\Models\Holiday;
use GuzzleHttp\Exception\GuzzleException;
use RezKit\Tours\Requests\ListHolidays;
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

        $rsp = $this->client->post('/holidays', ['body' => $body]);

        /**
         * @var Holiday $holiday
         */
        $holiday = $this->serializer->deserialize($rsp->getBody()->getContents(), Holiday::class, 'json');

        return $holiday;
    }
}
