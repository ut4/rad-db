<?php

namespace Rad\Db;

use JsonSerializable;

class JsonObject implements JsonSerializable
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $out = [];
        foreach ($this->__setProps as $propName) {
            if ($this->__omit && in_array($propName, $this->__omit)) {
                continue;
            }
            $out[$propName] = $this->{'get' . ucfirst($propName)}();
        }
        return $out;
    }
}