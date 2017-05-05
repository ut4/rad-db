<?php

namespace Rad\Db\Resources;

use Rad\Db\JsonObject as SmartJsonObject;

class Book extends SmartJsonObject
{
    protected $id;
    protected $title;
    protected $pagecount;

    public function setId($id)
    {
        $this->id = (int) $id;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getPagecount()
    {
        return $this->pagecount;
    }
    public function setPagecount($pagecount)
    {
        $this->pagecount = $pagecount ? (int) $pagecount : null;
    }
}
