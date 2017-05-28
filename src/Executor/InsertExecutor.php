<?php

namespace Rad\Db\Executor;

use Rad\Db\QueryExecutor;
use Rad\Db\Mapper;
use Rad\Db\QueryPlanPart;
use Rad\Db\QueryBuildingDb;

class InsertExecutor implements QueryExecutor
{
    private $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return int lastInsertId
     */
    public function exec(QueryPlanPart $qpp, QueryBuildingDb $db): int
    {
        if (isset($qpp->getData()[0])) {
            $dbMethod = 'insertMany';
            $mapMethod = 'mapAll';
        } else {
            $dbMethod = 'insert';
            $mapMethod = 'map';
        }
        return $db->$dbMethod(
            $qpp->getMapInstructor()->getTableName(),
            $this->mapper->$mapMethod(
                $qpp->getData(),
                $qpp->getMapInstructor()->getEntityClassPath(),
                [$qpp->getMapInstructor()->getIdColumnName()]
            )
        );
    }
}
