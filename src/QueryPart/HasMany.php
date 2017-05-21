<?php

namespace Rad\Db\QueryPart;

use Rad\Db\QueryPart;
use Rad\Db\PreProcessableQueryPart;

class HasMany extends QueryPart implements PreProcessableQueryPart
{
    public function preProcess(QueryPart $mainQueryPart)
    {
        for ($i = 0; $i < count($this->data); $i++) {
            $this->data[$i] += [
                $mainQueryPart->getMapInstructor()->getTableName() .
                ucfirst($mainQueryPart->getMapInstructor()->getIdColumnName()) =>
                        $mainQueryPart->result
            ];
        }
    }
}
