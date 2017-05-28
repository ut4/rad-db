<?php

namespace Rad\Db;

use Aura\SqlQuery\Common\SelectInterface;

class PlanExecutor
{
    private $db;

    public function __construct(QueryBuildingDb $db)
    {
        $this->db = $db;
    }

    /**
     * @return int
     */
    public function executeQueryPlan(
        array $planParts,
        QueryExecutor $executor
    ): int {
        $previous = null;
        // start transaction here ...
        foreach ($planParts as $planPart) {
            if ($planPart instanceof PreProcessableQueryPlanPart) {
                $planPart->preProcess($previous);
            }
            $planPart->result = $executor->exec($planPart, $this->db);
            // In case of failure, return early
            if ($planPart->result < 1) {
                return $planPart->result;
            }
            $previous = $planPart;
        }
        // Always return the result of the main query
        return $planParts[0]->result;
    }

    /**
     * @return array
     */
    public function executeFetchPlan(
        array $planParts,
        SelectInterface $select
    ): array {
        $fetchResults = $this->db->selectAll($select);
        $mapped = [];
        // Main rows, level 0
        $planParts[0]->collect($fetchResults, $mapped);
        // Hinted rows, level 1+
        while ($planPart = \next($planParts)) {
            $planPart->collect($fetchResults, $mapped);
        }
        return $mapped;
    }

    public function newSelect()
    {
        return $this->db->getQueryFactory()->newSelect();
    }
}
