<?php

namespace RezKit\Tours\Models;
class HolidayVersion extends Holiday
{
    private string $holidayId;

    /**
     * @return string
     */
    public function getHolidayId(): string
    {
        return $this->holidayId;
    }

    /**
     * @param string $holidayId
     */
    public function setHolidayId(string $holidayId): void
    {
        $this->holidayId = $holidayId;
    }
}
