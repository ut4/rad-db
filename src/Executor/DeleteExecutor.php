<?php

namespace Rad\Db\Executor;

use Rad\Db\QueryExecutor;
use Rad\Db\Mapper;
use Rad\Db\QueryPlanPart;
use Rad\Db\QueryBuildingDb;
use Aura\SqlQuery\Common\DeleteInterface;

class DeleteExecutor implements QueryExecutor
{
    private $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return int Number of affected rows
     */
    public function exec(QueryPlanPart $qpp, QueryBuildingDb $db): int
    {
        return $db->delete(
            $qpp->getMapInstructor()->getTableName(),
            function (DeleteInterface $q) use ($qpp) {
                $idCol = $qpp->getMapInstructor()->getIdColumnName();
                $input = $qpp->getData();
                $q->where($idCol . ' = :idVal');
                $q->bindValue(
                    'idVal',
                    isset($input[$idCol]) ? (int) $input[$idCol] : null
                );
            }
        );
    }
}
