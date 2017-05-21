<?php

namespace Rad\Db\Unit;

use Rad\Db\Planner;
use Rad\Db\QueryBuildingDb;
use Rad\Db\PlanExecutor;
use Rad\Db\Resources\AutoBookRepository;

class AutoRepositoryTests extends BaseTestCase
{
    use AutoRepositoryInsertTests;

    private $queryPlanner;
    private $mockQueryBuildingDb;
    private $queryPlanExecutor;
    private $bookRepository;

    /**
     * @before
     */
    public function beforeEach()
    {
        $this->queryPlanner = new Planner();
        $this->mockQueryBuildingDb = $this->createMock(QueryBuildingDb::class);
        $this->queryPlanExecutor = new PlanExecutor($this->mockQueryBuildingDb);
        $this->bookRepository = new AutoBookRepository(
            $this->queryPlanner,
            $this->queryPlanExecutor
        );
    }
}
