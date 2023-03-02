<?php

namespace RezKit\Tours\Responses;

use Countable;
use GuzzleHttp\Client;
use Iterator;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * A paginated API response which implements Countable and Iterator allowing for
 * simple iteration and functional application via foreach and map.
 *
 * @template T
 * @implements Iterator<int, T>
 */
class Paginated implements Countable, Iterator
{
    /**
     * @var int Total number of items in the paginated data set
     */
    private int $total;

    /**
     * @var int Current page number within the data set
     */
    private int $currentPage;

    /**
     * @var int Index of the last page of the data set
     */
    private int $lastPage;

    /**
     * @var int Current cursor position within the current page
     */
    private int $index = 0;

    /**
     * URL to the next page of data
     * @var string|null
     * @SerializedName("next_page_url")
     */
    private ?string $nextPageUrl;

    /**
     * @var int Maximum number of items on each page.
     */
    private int $perPage;

    /**
     * @var string|null
     * @SerializedName("first_page_url")
     */
    private ?string $firstPageUrl;

    /**
     * @var T[] $data
     */
    private array $data;

    /** @var Client Http Client interface */
    protected Client $client;

    /** @var Serializer Data Serializer */
    protected Serializer $serializer;

    public function __construct(
        Client $client,
        Serializer $serializer
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * @param int $lastPage
     */
    public function setLastPage(int $lastPage): void
    {
        $this->lastPage = $lastPage;
    }

    /**
     * @return string|null
     */
    public function getNextPageUrl(): ?string
    {
        return $this->nextPageUrl;
    }

    /**
     * @param string|null $nextPageUrl
     */
    public function setNextPageUrl(?string $nextPageUrl): void
    {
        $this->nextPageUrl = $nextPageUrl;
    }

    /**
     * @return string|null
     */
    public function getFirstPageUrl(): ?string
    {
        return $this->firstPageUrl;
    }

    /**
     * @param string|null $firstPageUrl
     */
    public function setFirstPageUrl(?string $firstPageUrl): void
    {
        $this->firstPageUrl = $firstPageUrl;
    }

    /**
     * @param T[] $data Data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function count(): int
    {
        return $this->getTotal();
    }

    public function current(): mixed
    {
        return $this->data[$this->index];
    }

    public function next(): void
    {
        $this->index++;

        // If we have seeked beyond the end of the page,
        // and we have more pages to get, then move to the next page.
        if ($this->index >= count($this->data) && is_string($this->getNextPageUrl())) {
            $this->loadPage($this->getNextPageUrl());
        }
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return (($this->currentPage - 1)*$this->perPage) + $this->index;
    }

    public function valid(): bool
    {
        return !($this->currentPage == $this->lastPage && $this->index >= count($this->data));
    }

    public function rewind(): void
    {
        if (is_string($this->getFirstPageUrl())) {
            $this->loadPage($this->getFirstPageUrl());
        }
    }

    private function loadPage(string $url): void
    {
        $rsp = $this->client->get($url);
        $body = $rsp->getBody()->getContents();

        $this->serializer->deserialize($body, static::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $this
        ]);

        $this->index = 0;
    }
}
