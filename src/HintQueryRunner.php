<?php

namespace Rad\Db;

use UnexpectedValueException;

final class HintQueryRunner
{
    /**
     * @param QueryPacket $mainQ
     * @param Callable $executor
     * @return int
     */
    public function run(QueryPacket $mainQ, Callable $executor): int
    {
        $sequence = $this->makeQueryPacketsFromHints($mainQ);
        $lastQ = $mainQ;
        //
        foreach ($sequence as $q) {
            // Prepare data
            $this->preProcessData($q, $lastQ);
            // Execute the query
            $result = call_user_func($executor, $q);
            if (!is_int($result)) {
                throw new UnexpectedValueException(
                    'Executor should return an integer'
                );
            }
            $q->setResult($result);
            // In case of failure, return the results
            if ($result < 1) {
                return $result;
            }
            // Otherwise, continue to the next query
            $lastQ = $q;
        }
        return $lastQ->getResult();
    }

    /**
     * @param QueryPacket $lastQ
     * @param &$sequence = []
     * @return SequenceItem[]|[]
     */
    private function makeQueryPacketsFromHints(
        QueryPacket $lastQ,
        array &$sequence = []
    ): array {
        $bindHints = $lastQ->getMapInstructor()->getBindHints();
        foreach ($bindHints as $hint) {
            $data = $lastQ->getData();
            if (!array_key_exists($hint->getTargetPropertyName(), $data)) {
                continue;
            }
            $instructorClassPath = $hint->getMapInstructorClassPath();
            $instructor = new $instructorClassPath();
            //
            $q = new QueryPacket($data[$hint->getTargetPropertyName()], $instructor);
            $q->setBindHint($hint);
            $sequence[] = $q;
            //
            if (($hints = $instructor->getBindHints())) {
                $this->getQuerySequence($q, $sequence);
            }
        }
        return $sequence;
    }

    /**
     * @param QueryPacket $q
     * @param QueryPacket $lastQ
     */
    private function preProcessData(QueryPacket $q, QueryPacket $lastQ)
    {
        $bindHint = $q->getBindHint();
        if (!method_exists($bindHint, 'preProcess')) {
            return;
        }
        $q->setData(
            isset($q->getData()[0])
                ? array_map(
                    function ($data) use ($bindHint, $lastQ) {
                        return $bindHint->preProcess($data, $lastQ);
                    },
                    $q->getData()
                )
                : $bindHint->preProcess($q->getData(), $lastQ)
        );
    }
}
