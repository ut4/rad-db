<?php

namespace Rad\Db;

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
    public function executeQueryPlan(array $planParts, Executor $executor): int
    {
        $previous = null;
        // start transaction here ...
        foreach ($planParts as $planPart) {
            if ($planPart instanceof PreProcessableQueryPart) {
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

    public function executeFetchPlan(array $planParts)
    {
        throw new Exception('no implemented yet');
    }
}
