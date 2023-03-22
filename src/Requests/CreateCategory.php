<?php

namespace RezKit\Tours\Requests;

class CreateCategory
{
    public string $name;

    public ?string $description;

    public ?bool $published;

    public ?bool $searchable;

    public ?string $parentId = null;
}
