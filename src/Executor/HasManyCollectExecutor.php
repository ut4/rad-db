<?php

namespace Rad\Db\Executor;

use Rad\Db\FetchPlanPart;
use RuntimeException;

class HasManyCollectExecutor extends RootCollectExecutor
{
    /**
     * Merges mapped data to &$mapped
     */
    public function exec(FetchPlanPart $fpp, array $fetchResults, array &$mapped)
    {
        $hint = $fpp->getBindHint();
        $batch = $this->collectBatch($fpp, $fetchResults);
        foreach ($batch as $item) {
            if (!$item->getId()) { // TODO - what to do with blank rows??
                continue;
            }
            $ref = &$this->findParent(
                $mapped,
                $item->{'get' . ucfirst($hint->getOriginIdCol())}(),
                $hint->getTargetPropertyName()
            );
            $ref[] = $item;
        }
    }

    /**
     * @throws RuntimeException
     * @return mixed
     */
    private function &findParent(
        array &$mapped,
        int $foreignKeyValue,
        string $targetPropertyName
    ) {
        foreach ($mapped as &$possibleParent) {
            // TODO fix hardcoded getId
            if ($possibleParent->getId() !== $foreignKeyValue) {
                continue;
            }
            if (!isset($possibleParent->$targetPropertyName)) {
                $possibleParent->$targetPropertyName = [];
                $possibleParent->markIsSet($targetPropertyName);
            }
            return $possibleParent->$targetPropertyName;
        }
        throw new RuntimeException(
            'Couldn\'t find the parent for ' . $targetPropertyName
        );
    }
}
