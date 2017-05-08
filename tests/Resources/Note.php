<?php

namespace Rad\Db\Resources;

use Rad\Db\JsonObject as SmartJsonObject;

class Note extends SmartJsonObject
{
    protected $id;
    protected $content;
    protected $booksId;

    public function setId($id)
    {
        $this->id = (int) $id;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content)
    {
        return $this->content = $content;
    }

    public function setBooksId($booksId)
    {
        $this->booksId = (int) $booksId;
    }
    public function getBooksId(): int
    {
        return $this->booksId;
    }
}
