<?php

namespace RezKit\Tours\Requests;

class ListCategories extends PaginationQuery
{
    public ?string $name;

    public ?string $search;

    public string $published = BooleanParam::UNDEFINED;

    public string $searchable = BooleanParam::UNDEFINED;

    public string $trash = BooleanParam::FALSE;

    public ?bool $children;
}
