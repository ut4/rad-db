<?php

namespace Rad\Db;

use JsonSerializable;

class JsonObject implements JsonSerializable
{
    /**
     * TODO fix this madness
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $out = [];
        if (isset($this->__keys)) {
            foreach (get_class_methods($this) as $methodName) {
                if (substr($methodName, 0, 3) !== 'get') {
                    continue;
                }
                $out[lcfirst(substr($methodName, 3))] = 1;
            }
            return $out;
        }
        foreach ($this->__setProps as $propName) {
            if ($this->__omit && in_array($propName, $this->__omit)) {
                continue;
            }
            $out[$propName] = $this->{'get' . ucfirst($propName)}();
        }
        return $out;
    }
}