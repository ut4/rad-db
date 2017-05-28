<?php

namespace Demo;

use Rad\Db\JsonObject;

class TodoItem extends JsonObject
{
    protected $id;
    protected $desc;
    protected $due;

    public function setId($id)
    {
        $this->id = (int) $id;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function setDescription(string $desc)
    {
        $this->desc = $desc;
    }
    public function getDescription(): string
    {
        return $this->desc;
    }

    public function setDue(string $due)
    {
        $this->due = new CoolDateTime($due);
    }
    public function getDue(): CoolDateTime
    {
        return $this->due;
    }
}
