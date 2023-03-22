<?php

namespace RezKit\Tours\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use RezKit\Tours\Models\Category;
use RezKit\Tours\Requests\CreateCategory;
use RezKit\Tours\Requests\ListCategories;
use RezKit\Tours\Responses\Paginated;
use RezKit\Tours\Responses\PaginatedCategories;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class Categories extends Endpoint
{

    private string $type;

    public function __construct(GuzzleClient $client, string $type)
    {
        parent::__construct($client);
        $this->type = $type;
    }

    /**
     * List Categories
     *
     * @param ListCategories|null $params
     * @return Paginated<Category>
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list(?ListCategories $params = null): Paginated
    {
        $params ??= new ListCategories();

        $rsp = $this->client->request('GET', "/{$this->type}/categories", [
            'query' => $this->serializer->normalize($params),
        ]);

        $body = $rsp->getBody()->getContents();

        $paginator = new PaginatedCategories($this->client, $this->serializer);

        $this->serializer->deserialize($body, PaginatedCategories::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $paginator
        ]);

        return $paginator;
    }

    public function find(string $id): Category
    {
        $rsp = $this->client->request('GET', "/{$this->type}/categories/{$id}");

        /**
         * @var Category $category
         */
        $category = $this->serializer->deserialize($rsp->getBody()->getContents(), Category::class, 'json');

        return $category;
    }

    public function create(CreateCategory $params): Category
    {
        $body = $this->serializer->serialize($params, 'json');

        $rsp = $this->client->request('POST', "/{$this->type}/categories", [
            RequestOptions::BODY => $body,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json'
            ]
        ]);

        /** @var Category $c */
        $c = $this->serializer->deserialize($rsp->getBody()->getContents(), Category::class, 'json');
        return $c;
    }

    public function delete(string $id): void
    {
        $this->client->request('DELETE', "/{$this->type}/categories/{$id}");
    }
}
