<?php

namespace Rad\Db\Unit;

use Rad\Db\Planner;
use Rad\Db\QueryBuildingDb;
use Rad\Db\PlanExecutor;
use Rad\Db\Resources\AutoBookRepository;
use Rad\Db\Mapper;
use Rad\Db\Resources\Book;

class AutoRepositoryTests extends BaseTestCase
{
    use AutoRepositoryInsertTests;
    use AutoRepositorySelectTests;

    private $queryPlanner;
    private $mockQueryBuildingDb;
    private $queryPlanExecutor;
    private $mapper;
    private $bookRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        $this->queryPlanner = new Planner();
        $this->mockQueryBuildingDb = $this->createMock(QueryBuildingDb::class);
        $this->queryPlanExecutor = new PlanExecutor($this->mockQueryBuildingDb);
        $this->mapper = new Mapper(Book::class);
        $this->bookRepository = new AutoBookRepository(
            $this->queryPlanner,
            $this->queryPlanExecutor,
            $this->mapper
        );
    }
}
