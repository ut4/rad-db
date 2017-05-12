<?php

namespace Rad\Db\Resources;

use Rad\Db\Resources\Book;
use Rad\Db\BindHint\OneToMany;
use Rad\Db\Mappable;

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
        return [new OneToMany('notes', NoteMapInstructor::class)];
    }

    public function getIdColumnName(): string
    {
        return 'id';
    }
}
