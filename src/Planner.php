<?php

namespace Rad\Db;

use Aura\SqlQuery\Common\SelectInterface;
use Rad\Db\Executor\RootCollectExecutor;

class Planner
{
    private $fetchPlanningUtils;

    /**
     * @return QueryPlanPart[]
     */
    public function makeQueryPlan(
        array $data,
        Mappable $instructor,
        BindHint $hint = null,
        array &$plan = []
    ): array {
        // Main query part
        if (!$hint) {
            $queryPlanPartClassPath = QueryPlanPart::class;
        // Hinted query part
        } else {
            $queryPlanPartClassPath = sprintf(
                'Rad\Db\QueryPlanPart\%s',
                $hint->getBindType()
            );
        }
        $plan[] = new $queryPlanPartClassPath($data, $instructor);
        //
        if ($instructor->getBindHints()) {
            $parentData = $plan[count($plan) - 1]->getData();
            foreach ($instructor->getBindHints() as $hint) {
                if (!isset($parentData[$hint->getTargetPropertyName()])) {
                    continue;
                }
                $cp = $hint->getMapInstructorClassPath();
                $instructor = new $cp();
                $this->makeQueryPlan(
                    $parentData[$hint->getTargetPropertyName()],
                    $instructor,
                    $hint,
                    $plan
                );
            }
        }
        return $plan;
    }

    /**
     * @return FetchPlanPart[]
     */
    public function makeFetchPlan(
        SelectInterface $select,
        Mappable $instructor
    ) {
        // Collect & remove original select columns
        $selectColumns = \array_flip($select->getCols());
        \array_map([$select, 'removeCol'], $selectColumns);
        // Expand select columns & make the plan
        $this->fetchPlanningUtils = new FetchPlanningUtils();
        $planParts = $this->makeFetchPlanParts($selectColumns, $instructor);
        // Apply expanded columns
        foreach ($planParts as $planPart) {
            $select->cols($planPart->getSelectColumns());
        }
        return $planParts;
    }

    /**
     * @return FetchPlanPart[]
     */
    private function makeFetchPlanParts(
        array $selectColumns,
        Mappable $instructor,
        BindHint $hint = null,
        array &$plan = []
    ): array {
        //
        if (!$hint) {
            $executorCp = RootCollectExecutor::class;
        } else {
            $executorCp = sprintf(
                'Rad\Db\Executor\%sCollectExecutor',
                $hint->getBindType()
            );
        }
        $plan[] = new FetchPlanPart(
            $this->fetchPlanningUtils->collectTargetColumns(
                $selectColumns,
                $hint ? $hint->getTargetPropertyName() : ''
            ),
            $instructor,
            new $executorCp(new Mapper($instructor->getEntityClassPath())),
            $hint
        );
        //
        if ($instructor->getBindHints()) {
            $hintOrigin = $plan[\count($plan) - 1];
            foreach ($instructor->getBindHints() as $hint) {
                if (!$this->fetchPlanningUtils->targetColumnExists(
                    $selectColumns,
                    $hint->getTargetPropertyName()
                )) {
                    continue;
                }
                $hint->setOriginIdCol(
                    $hintOrigin->getMapInstructor()->getTableName() .
                    \ucfirst($hintOrigin->getMapInstructor()->getIdColumnName())
                );
                $instructorClassPath = $hint->getMapInstructorClassPath();
                $this->makeFetchPlanParts(
                    $selectColumns,
                    new $instructorClassPath(),
                    $hint,
                    $plan
                );
            }
        }
        return $plan;
    }
}
