<?php

namespace Rad\Db\Resources;

use Rad\Db\Mappable;
use Rad\Db\Resources\Book;
use Rad\Db\BindHint;
use Rad\Db\Resources\NoteMappings;
use Rad\Db\QueryPart\HasMany;

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
        return [new BindHint('notes', HasMany::class, NoteMappings::class)];
    }

    public function getIdColumnName(): string
    {
        return 'id';
    }
}
