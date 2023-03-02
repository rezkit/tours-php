<?php

namespace RezKit\Tours\Responses;

use RezKit\Tours\Models\Holiday;

/**
 * @extends Paginated<Holiday>
 */
class PaginatedHolidays extends Paginated
{
    /**
     * @var Holiday[] $data
     */
    private array $data;
}
