<?php

namespace RezKit\Tours\Responses;

use Countable;
use Iterator;
use GuzzleHttp\Client;
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
    private array $data = [];

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
     * @see count
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @internal
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    /**
     * Get the maximum number of items per page
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @internal
     * @param int $perPage
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * Get the current page number
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @internal
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * Get the last page number
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * @internal
     * @param int $lastPage
     */
    public function setLastPage(int $lastPage): void
    {
        $this->lastPage = $lastPage;
    }

    /**
     * Get the URL for the next page of data
     * @return string|null
     */
    public function getNextPageUrl(): ?string
    {
        return $this->nextPageUrl;
    }

    /**
     * @internal
     * @param string|null $nextPageUrl
     */
    public function setNextPageUrl(?string $nextPageUrl): void
    {
        $this->nextPageUrl = $nextPageUrl;
    }

    /**
     * Get the URL for the first page of data
     * @return string|null
     */
    public function getFirstPageUrl(): ?string
    {
        return $this->firstPageUrl;
    }

    /**
     * @internal
     * @param string|null $firstPageUrl
     */
    public function setFirstPageUrl(?string $firstPageUrl): void
    {
        $this->firstPageUrl = $firstPageUrl;
    }

    /**
     * @internal
     * @param T[] $data Data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the total count of items in the dataset
     *
     * @return int Total count of items in the dataset
     */
    public function count(): int
    {
        return $this->getTotal();
    }

    /**
     * Get the current item
     *
     * @return T Current Item
     */
    public function current(): mixed
    {
        return $this->data[$this->index];
    }

    /**
     * Iterate to the next item in the dataset
     *
     * @return void
     */
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
     * Get the key of the current item within the dataset.
     *
     * The key is the index position of the current item within the whole dataset:
     *
     * @return int
     */
    public function key(): int
    {
        return (($this->currentPage - 1)*$this->perPage) + $this->index;
    }

    /**
     * Check the iterator is valid.
     *
     * Once this function returns false, the iterator is no longer valid,
     * we have reached the end of the dataset.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return !($this->currentPage == $this->lastPage && $this->index >= count($this->data));
    }

    /**
     * Rewind the Iterator to the start of the dataset
     * @return void
     */
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
