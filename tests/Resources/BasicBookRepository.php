<?php

namespace Rad\Db\Resources;

use Rad\Db\BasicCrudRepository;
use Rad\Db\Resources\BookMappings;

class BasicBookRepository extends BasicCrudRepository
{
    /**
     * @return string
     */
    public function getMapInstructorClassPath(): string
    {
        return BookMappings::class;
    }
}
