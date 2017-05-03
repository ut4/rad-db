<?php

namespace Rad\Db;

use JsonSerializable;

class JsonObject implements JsonSerializable
{
    /**
     * Otherwise we have no idea which properties should be serialized when
     * jsonSerialize is triggered by json_encode.
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
            $out[$propName] = $this->{'get' . ucfirst($propName)}();
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