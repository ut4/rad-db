<?php

namespace Rad\Db\Resources;

use JsonSerializable;

class JsonObject implements JsonSerializable
{
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
