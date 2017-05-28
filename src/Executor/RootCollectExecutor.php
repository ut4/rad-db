<?php

namespace Rad\Db\Executor;

use Rad\Db\Mapper;
use Rad\Db\FetchPlanPart;
use Rad\Db\CollectExecutor;

class RootCollectExecutor implements CollectExecutor
{
    protected $mapper;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Merges mapped data to &$mapped
     */
    public function exec(FetchPlanPart $fpp, array $fetchResults, array &$mapped)
    {
        $mapped = \array_merge($mapped, $this->collectBatch($fpp, $fetchResults));
    }

    /**
     * @return array Mapped data as an associative array
     */
    protected function collectBatch(FetchPlanPart $fpp, array $fetchResults)
    {
        return \array_map(
            [$this->mapper, 'map'],
            $this->collectData($fpp, $fetchResults)
        );
    }

    /**
     * @return array
     */
    private function collectData(FetchPlanPart $fpp, array $fetchResults): array
    {
        $primaryKeyColumn = $fpp->getMapInstructor()->getIdColumnName();
        if (($hint = $fpp->getBindHint())) {
            $primaryKeyColumn = $hint->getTargetPropertyName() . '.' . $primaryKeyColumn;
        }
        $relevantData = [];
        $selectColumns = $fpp->getSelectColumns();
        foreach ($fetchResults as $row) {
            if (\array_key_exists($row[$primaryKeyColumn], $relevantData)) {
                continue;
            }
            $filteredRow = [];
            foreach ($selectColumns as $tablePath => $colPath) {
                $tablePathParts = \explode('.', $tablePath);
                $filteredRow[\end($tablePathParts)] = $row[\str_replace('"', '', $colPath)];
            }
            $relevantData[$row[$primaryKeyColumn]] = $filteredRow;
        }
        return \array_values($relevantData);
    }
}
