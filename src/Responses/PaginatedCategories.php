<?php

namespace RezKit\Tours\Responses;

use RezKit\Tours\Models\Category;

/**
 * @extends Paginated<Category>
 */
class PaginatedCategories extends Paginated
{
    /**
     * @var Category[] $data
     */
    private array $data;
}
