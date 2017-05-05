<?php

namespace Rad\Db\Resources;

use Rad\Db\BasicCrudRepository;
use Rad\Db\Resources\Book;

class BookRepository extends BasicCrudRepository
{
    public function getEntityClassPath(): string
    {
        return Book::class;
    }

    public function getTableName(): string
    {
        return 'books';
    }
}
