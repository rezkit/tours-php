<?php

namespace RezKit\Tours\Requests;

class ListHolidays extends PaginationQuery
{
    public ?string $name;

    public ?string $code;

    public ?string $search;
}
