<?php

namespace RezKit\Tours\Responses;

use RezKit\Tours\Models\HolidayVersion;

/**
 * @inheritdoc
 * @extends Paginated<HolidayVersion>
 */
class PaginatedVersions extends Paginated
{
    /**
     * @var HolidayVersion[] $data
     */
    private array $data = [];
}
