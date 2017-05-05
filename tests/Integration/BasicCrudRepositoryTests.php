<?php

namespace Rad\Db\Integration;

use Rad\Db\Db;
use Rad\Db\QueryBuildingDb;
use Rad\Db\Resources\BookRepository;

class BasicCrudRepositoryTests extends InMemoryPDOTestCase
{
    use BasicCrudRepositoryInsertTests;
    use BasicCrudRepositorySelectTests;
    use BasicCrudRepositoryUpdateTests;
    use BasicCrudRepositoryDeleteTests;

    private $queryBuildingDb;
    private $bookRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        parent::beforeEach();
        $this->queryBuildingDb = new QueryBuildingDb(
            new Db($this->connection),
            $this->queryFactory
        );
        $this->bookRepository = new BookRepository(
            $this->queryBuildingDb
        );
    }
}
