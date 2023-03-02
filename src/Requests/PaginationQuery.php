<?php

namespace RezKit\Tours\Requests;

class PaginationQuery
{
    public int $page = 1;
    public ?int $limit;
    public string $trash = BooleanParam::UNDEFINED;
}
