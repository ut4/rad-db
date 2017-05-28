<?php

namespace Rad\Db\QueryPlanPart;

use Rad\Db\QueryPlanPart;
use Rad\Db\PreProcessableQueryPlanPart;

class HasMany extends QueryPlanPart implements PreProcessableQueryPlanPart
{
    public function preProcess(QueryPlanPart $mainQueryPlanPart)
    {
        for ($i = 0; $i < count($this->data); $i++) {
            $this->data[$i] += [
                $mainQueryPlanPart->getMapInstructor()->getTableName() .
                ucfirst($mainQueryPlanPart->getMapInstructor()->getIdColumnName()) =>
                        $mainQueryPlanPart->result
            ];
        }
    }
}
