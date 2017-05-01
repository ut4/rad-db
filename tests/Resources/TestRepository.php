<?php

namespace Rad\Db\Resources;

use Rad\Db\BasicCrudRepository;
use Rad\Db\Resources\TestTableEntity;

class TestRepository extends BasicCrudRepository
{
    public function getEntityClassPath(): string
    {
        return TestTableEntity::class;
    }

    public function getTableName(): string
    {
        return 'test_table';
    }
}
