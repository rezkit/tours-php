<?php

namespace RezKit\Tours\Requests;

class UpdateHolidayRequest
{
    public ?string $code;

    public ?string $name;

    public ?string $introduction;

    public ?string $ordering;

    public ?string $description;
}
