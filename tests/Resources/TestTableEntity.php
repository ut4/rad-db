<?php

namespace Rad\Db\Resources;

use Rad\Db\JsonObject as SmartJsonObject;

class TestTableEntity extends SmartJsonObject
{
    protected $id;
    protected $somecol;
    protected $number;

    public function setId($id)
    {
        return $this->id = (int) $id;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getSomecol(): string
    {
        return $this->somecol;
    }
    public function setSomecol(string $somecol)
    {
        return $this->somecol = $somecol;
    }

    public function getNumber()
    {
        return $this->number;
    }
    public function setNumber($number)
    {
        $this->number = $number ? (int) $number : null;
    }
}
