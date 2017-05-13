<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\HintQueryRunner;
use Rad\Db\QueryPacket;
use Rad\Db\Resources\BookMappings;

class HintQueryRunnerTests extends TestCase
{
    private $mainQuery;
    private $hintQueryRunner;

    private function manualBeforeEach(array $testInputData)
    {
        $this->mainQuery = new QueryPacket($testInputData, new BookMappings());
        $this->mainQuery->setResult(45);
        $this->hintQueryRunner = new HintQueryRunner();
    }

    public function testRunExecutesHintedQueries()
    {
        $actuallyExecuted = [];
        $executor = function (QueryPacket $q) use (&$actuallyExecuted) {
            // executing some database query ...
            $actuallyExecuted[] = $q;
            return 1;
        };
        $hintTargetData = ['content' => 'foo'];
        $this->manualBeforeEach(['title' => 'afo', 'notes' => $hintTargetData]);
        // Execute
        $this->hintQueryRunner->run($this->mainQuery, $executor);
        // Assert
        $this->assertEquals($hintTargetData['content'], $actuallyExecuted[0]->getData()['content']);
    }

    public function testRunCallsPreProcessorOneAtATime()
    {
        $actuallyExecuted = [];
        $executor = function (QueryPacket $q) use (&$actuallyExecuted) {
            // executing some database query ...
            $actuallyExecuted[] = $q;
            return 1;
        };
        $testInput = ['title' => 'afo', 'notes' => [['content' => 'bar'], ['content' => 'foo']]];
        $this->manualBeforeEach($testInput);
        // Execute
        $this->hintQueryRunner->run($this->mainQuery, $executor);
        // Assert
        $this->assertCount(1, $actuallyExecuted);
        $bindHint = $this->mainQuery->getMapInstructor()->getBindHints()[0];
        $this->assertEquals(
            [
                $bindHint->preProcess($testInput['notes'][0], $this->mainQuery),
                $bindHint->preProcess($testInput['notes'][1], $this->mainQuery)
            ],
            $actuallyExecuted[0]->getData()
        );
    }

    public function testRunDoesNothingIfHintTargetIsNotThere()
    {
        $runHintQueryCount = 0;
        $executor = function () use (&$runHintQueryCount) {
            $runHintQueryCount++;
            return 1;
        };
        $this->manualBeforeEach(['title' => 'afo']);
        // Execute
        $this->hintQueryRunner->run($this->mainQuery, $executor);
        // Assert
        $this->assertEquals(0, $runHintQueryCount);
    }

    public function testRunReturnsEarlyIfExecutorFails()
    {
        $executor = function () {
            return 0;
        };
        $this->manualBeforeEach(['title' => 'afo', 'notes' => ['content' => 'foo']]);
        // Execute
        $result = $this->hintQueryRunner->run($this->mainQuery, $executor);
        // Assert
        $this->assertEquals(0, $result);
    }
}
