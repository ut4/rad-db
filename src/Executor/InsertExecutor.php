<?php

namespace Rad\Db\Executor;

use Rad\Db\Executor;
use Rad\Db\Mapper;
use Rad\Db\QueryPart;
use Rad\Db\QueryBuildingDb;

class InsertExecutor implements Executor
{
    private $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function exec(QueryPart $qp, QueryBuildingDb $db): int
    {
        if (isset($qp->getData()[0])) {
            return $db->insertMany(
                $qp->getMapInstructor()->getTableName(),
                $this->mapper->mapAll(
                    $qp->getData(),
                    $qp->getMapInstructor()->getEntityClassPath(),
                    [$qp->getMapInstructor()->getIdColumnName()]
                )
            );
        }
        return $db->insert(
            $qp->getMapInstructor()->getTableName(),
            $this->mapper->map(
                $qp->getData(),
                $qp->getMapInstructor()->getEntityClassPath(),
                [$qp->getMapInstructor()->getIdColumnName()]
            )
        );
    }
}
