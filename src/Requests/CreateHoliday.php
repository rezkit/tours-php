<?php

namespace RezKit\Tours\Requests;

class CreateHoliday
{
    public string $name;

    public string $code;

    public ?string $introduction;

    public ?string $description;
}
