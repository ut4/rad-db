<?php

namespace Rad\Db;

use Closure;
use JsonSerializable;
use Rad\Db\Executor\InsertExecutor;

abstract class AutoRepository implements Repository
{
    protected $planner;
    protected $rootMapInstructor;
    protected $planExecutor;

    public function __construct(
        Planner $planner,
        PlanExecutor $planExecutor
    ) {
        $this->planner = $planner;
        $this->planExecutor = $planExecutor;
        $cp = $this->getMapInstructorClassPath();
        $this->rootMapInstructor = new $cp();
    }

    public abstract function getMapInstructorClassPath(): string;

    /**
     * $myRepo->insert([
     *     'foo' => 'bar',
     *     'hintarget' => [
     *         'bar' => 'bar'
     *     ]
     * ]);
     *
     * @param array $data Input data as an associative array
     * @return int Insert id of the main query
     */
    public function insert(array $data): int
    {
        $planParts = $this->planner->makeQueryPlan($data, $this->rootMapInstructor);
        return $this->planExecutor->executeQueryPlan($planParts, new InsertExecutor(new Mapper()));
    }

    /**
     * $myRepo->selectAll(function (Aura\SqlQuery\Common\SelectInterface $s) {
     *     $s->from('foo as f');
     *     $s->innerJoin('bar as b', 'b.fid = f.id');
     *     $s->cols(['f.foo as foo', 'b.bar as hinttarget.bar']);
     * });
     *
     * @param Closure $queryBuilderSetupFn = null
     * @return array The mapped results
     */
    public function selectAll(Closure $queryBuilderSetupFn = null): array
    {
        $select = $this->planExecutor->newSelect();
        $queryBuilderSetupFn($select);
        $fetchPlanParts = $this->planner->makeFetchPlan($select, $this->rootMapInstructor);
        return $this->planExecutor->executeFetchPlan($fetchPlanParts, $select);
    }

    public function findAll(Callable $filterApplier): array
    {
        throw new Exception('not implemented yet');
    }

    public function findOne(Callable $filterApplier): JsonSerializable
    {
        throw new Exception('not implemented yet');
    }

    public function update(array $input): int
    {
        throw new Exception('not implemented yet');
    }

    public function delete(array $input): int
    {
        throw new Exception('not implemented yet');
    }
}
