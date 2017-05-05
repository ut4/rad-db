<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\QueryBuildingDb;
use Rad\Db\BasicMapper;
use Rad\Db\Resources\BookRepository;

class BasicCrudRepositoryTests extends TestCase
{
    use BasicCrudRepositoryInsertTests;
    use BasicCrudRepositorySelectTests;
    use BasicCrudRepositoryUpdateTests;
    use BasicCrudRepositoryDeleteTests;

    private $mockQueryBuildingDb;
    private $mockMapper;
    private $bookRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        $this->mockQueryBuildingDb = $this->createMock(QueryBuildingDb::class);
        $this->mockMapper = $this->createMock(BasicMapper::class);
        $this->bookRepository = new BookRepository(
            $this->mockQueryBuildingDb,
            $this->mockMapper
        );
    }
}
