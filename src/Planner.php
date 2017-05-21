<?php

namespace Rad\Db;

class Planner
{
    /**
     * @return QueryPart[]
     */
    public function makeQueryPlan(
        array $data,
        Mappable $instructor,
        BindHint $hint = null,
        $targetData = null,
        array &$plan = []
    ): array {
        // Main query part
        if (!$hint) {
            $queryPartClassPath = QueryPart::class;
        // Hinted query part
        } else {
            $queryPartClassPath = $hint->getQueryPartClassPath();
        }
        $plan[] = new $queryPartClassPath($targetData ?? $data, $instructor);
        //
        if ($instructor->getBindHints()) {
            $parentData = $plan[count($plan) - 1]->getData();
            foreach ($instructor->getBindHints() as $hint) {
                if (!isset($parentData[$hint->getTargetPropertyName()])) {
                    continue;
                }
                $cp = $hint->getMapInstructorClassPath();
                $instructor = new $cp();
                $this->makeQueryPlan($data, $instructor, $hint, $parentData[$hint->getTargetPropertyName()], $plan);
            }
        }
        return $plan;
    }

    public function makeFetchPlan(
        array $keyPaths,
        BindHint $hint = null,
        &$plan = []
    ) {
        throw new Exception('no implemented yet');
    }
}
