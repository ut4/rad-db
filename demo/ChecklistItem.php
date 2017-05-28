<?php

namespace Demo;

use Rad\Db\JsonObject;

class ChecklistItem extends JsonObject
{
    protected $id;
    protected $description;
    protected $todoItemId;

    public function setId($id)
    {
        $this->id = (int) $id;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setTodoItemId($todoItemId)
    {
        $this->todoItemId = (int) $todoItemId;
    }
    public function getTodoItemId(): int
    {
        return $this->todoItemId;
    }
}
