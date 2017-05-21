<?php

namespace Rad\Db;

use JsonSerializable;
use Rad\Db\Executor\InsertExecutor;

abstract class AutoRepository implements Repository
{
    public function __construct(
        Planner $planner,
        PlanExecutor $planExecutor
    ) {
        $this->planner = $planner;
        $cp = $this->getMapInstructorClassPath();
        $this->rootMapInstructor = new $cp();
        $this->planExecutor = $planExecutor;
    }

    public abstract function getMapInstructorClassPath(): string;

    public function insert(array $data): int
    {
        $planParts = $this->planner->makeQueryPlan($data, $this->rootMapInstructor);
        return $this->planExecutor->executeQueryPlan($planParts, new InsertExecutor(new Mapper()));
    }

    public function selectAll(): array
    {
        throw new Exception('not implemented yet');
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
