<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\QueryBuildingDb;
use Rad\Db\Mapper;
use Rad\Db\Resources\BasicBookRepository;
use Rad\Db\Resources\BookMappings;

class BasicCrudRepositoryTests extends TestCase
{
    use BasicCrudRepositoryInsertTests;
    use BasicCrudRepositorySelectTests;
    use BasicCrudRepositoryUpdateTests;
    use BasicCrudRepositoryDeleteTests;

    private $mockQueryBuildingDb;
    private $mockMapper;
    private $bookMapInstructor;
    private $bookRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        $this->mockQueryBuildingDb = $this->createMock(QueryBuildingDb::class);
        $this->mockMapper = $this->createMock(Mapper::class);
        $this->bookMapInstructor = new BookMappings();
        $this->bookRepository = new BasicBookRepository(
            $this->mockQueryBuildingDb,
            $this->mockMapper
        );
    }
}
