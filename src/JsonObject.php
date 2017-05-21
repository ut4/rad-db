<?php

namespace Rad\Db;

use JsonSerializable;

class JsonObject implements JsonSerializable
{
    /**
     * Otherwise we have no idea which properties should be included to the
     * output of jsonSerialize when its triggered by json_encode (or manually).
     */
    protected $mappedProps = [];
    protected $propsToOmit;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $out = [];
        foreach ($this->mappedProps as $propName) {
            if ($this->propsToOmit && in_array($propName, $this->propsToOmit)) {
                continue;
            }
            $getterName = 'get' . ucfirst($propName);
            $out[$propName] = method_exists($this, $getterName) ? $this->$getterName() : $this->$propName;
        }
        return $out;
    }

    /**
     * @param string $propName
     */
    public function markIsSet(string $propName)
    {
        $this->mappedProps[] = $propName;
    }
    /**
     * @param array $propsToOmit = null
     */
    public function omitThese(array $propsToOmit = null)
    {
        $propsToOmit && $this->propsToOmit = $propsToOmit;
    }
}