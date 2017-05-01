<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\QueryBuildingDb;
use Rad\Db\BasicMapper;
use Rad\Db\Resources\TestRepository;

class BasicCrudRepositoryTests extends TestCase
{
    use BasicCrudRepositoryInsertTests;
    use BasicCrudRepositorySelectTests;
    use BasicCrudRepositoryUpdateTests;
    use BasicCrudRepositoryDeleteTests;

    private $mockQueryBuildingDb;
    private $mockMapper;
    private $testRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        $this->mockQueryBuildingDb = $this->createMock(QueryBuildingDb::class);
        $this->mockMapper = $this->createMock(BasicMapper::class);
        $this->testRepository = new TestRepository(
            $this->mockQueryBuildingDb,
            $this->mockMapper
        );
    }
}
