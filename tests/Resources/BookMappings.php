<?php

namespace Rad\Db\Resources;

use Rad\Db\Mappable;
use Rad\Db\Resources\Book;
use Rad\Db\BindHint;
use Rad\Db\Resources\NoteMappings;

class BookMappings implements Mappable
{
    public function getTableName(): string
    {
        return 'books';
    }

    public function getEntityClassPath(): string
    {
        return Book::class;
    }

    public function getBindHints(): array
    {
        return [new BindHint('HasMany', 'notes', NoteMappings::class)];
    }

    public function getIdColumnName(): string
    {
        return 'id';
    }
}
