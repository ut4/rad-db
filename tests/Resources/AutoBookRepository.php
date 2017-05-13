<?php

namespace Rad\Db\Resources;

use Rad\Db\AutoRepository;
use Rad\Db\Resources\BookMappings;

class AutoBookRepository extends AutoRepository
{
    /**
     * @return string
     */
    public function getMapInstructorClassPath(): string
    {
        return BookMappings::class;
    }
}
