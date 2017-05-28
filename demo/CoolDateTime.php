<?php

namespace Demo;

use DateTime;
use JsonSerializable;

class CoolDateTime extends DateTime implements JsonSerializable
{
    const ISO_DATE_ONLY = 'Y-m-d';

    public function jsonSerialize()
    {
        return $this->format(self::ISO_DATE_ONLY);
    }
}
