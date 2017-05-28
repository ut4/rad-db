<?php

namespace Rad\Db;

use Closure;
use JsonSerializable;
use Rad\Db\Executor\InsertExecutor;
use Rad\Db\Executor\UpdateExecutor;
use Rad\Db\Executor\DeleteExecutor;

abstract class AutoRepository implements Repository
{
    private $planner;
    private $planExecutor;
    private $mapper;

    protected $rootMapInstructor;

    public function __construct(
        Planner $planner,
        PlanExecutor $planExecutor,
        Mapper $mapper = null
    ) {
        $this->planner = $planner;
        $this->planExecutor = $planExecutor;
        $cp = $this->getMapInstructorClassPath();
        $this->rootMapInstructor = new $cp();
        $this->mapper = $mapper ?? new Mapper($this->rootMapInstructor->getEntityClassPath());
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
        return $this->planExecutor->executeQueryPlan($planParts, new InsertExecutor($this->mapper));
    }

    /**
     * $myRepo->selectAll(function (Aura\SqlQuery\Common\SelectInterface $s) {
     *     $s->from('foo as f');
     *     $s->innerJoin('bar as b', 'b.fid = f.id');
     *     $s->cols(['f.foo as foo', 'b.bar as hinttarget.bar']);
     * });
     *
     * @param Closure $queryBuilderSetupFn = null
     * @param Closure $filterApplier = null
     * @return array The mapped results
     */
    public function selectAll(
        Closure $queryBuilderSetupFn = null,
        Closure $filterApplier = null
    ): array {
        $select = $this->planExecutor->newSelect();
        $queryBuilderSetupFn($select);
        $filterApplier && $filterApplier($select);
        $fetchPlanParts = $this->planner->makeFetchPlan($select, $this->rootMapInstructor);
        return $this->planExecutor->executeFetchPlan($fetchPlanParts, $select);
    }

    /**
     * $myRepo->findAll(function (Aura\SqlQuery\Common\SelectInterface $s) {
     *      $s->where('somekey IN(:bindMe, :bindMeToo)');
     *      $s->bindValue('bindMe', '1');
     *      $s->bindValue('bindMeToo', '2');
     * });
     *
     * @param Closure $filterApplier
     * @param Closure $queryBuilderSetupFn = null
     * @return array The mapped results
     */
    public function findAll(
        Closure $filterApplier,
        Closure $queryBuilderSetupFn = null
    ): array {
        return $this->selectAll($queryBuilderSetupFn, $filterApplier);
    }

    /**
     * $myRepo->findOne(function (Aura\SqlQuery\Common\SelectInterface $s) {
     *      $s->where('somekey = :bindMe');
     *      $s->bindValue('bindMe', '1');
     * });
     *
     * @param Closure $filterApplier
     * @param Closure $queryBuilderSetupFn = null
     * @return array The mapped results
     */
    public function findOne(
        Closure $filterApplier,
        Closure $queryBuilderSetupFn = null
    ): JsonSerializable {
        return $this->selectAll($queryBuilderSetupFn, $filterApplier)[0];
    }

    /**
     * $myRepo->update(['idCol' => '2', 'key' => 'updatedFoo']);
     *
     * @param array $input
     * @return int Number of affected rows
     */
    public function update(array $input): int
    {
        return $this->planExecutor->executeQueryPlan(
            [new QueryPlanPart($input, $this->rootMapInstructor)],// TODO recursive
            new UpdateExecutor($this->mapper)
        );
    }

    /**
     * $myRepo->delete(['idCol' => '2']);
     *
     * @param array $input
     * @return int Number of affected rows
     */
    public function delete(array $input): int
    {
        return $this->planExecutor->executeQueryPlan(
            [new QueryPlanPart($input, $this->rootMapInstructor)],// TODO recursive
            new DeleteExecutor($this->mapper)
        );
    }
}
