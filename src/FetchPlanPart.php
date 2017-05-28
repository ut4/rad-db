<?php

namespace Rad\Db;

class FetchPlanPart
{
    private $selectColumns;
    private $instructor;
    private $executor;
    private $bindHint;

    public function __construct(
        array $selectColumns,
        Mappable $instructor,
        CollectExecutor $executor,
        BindHint $bindHint = null
    ) {
        $this->selectColumns = $selectColumns;
        $this->instructor = $instructor;
        $this->executor = $executor;
        $this->bindHint = $bindHint;
    }

    public function getSelectColumns(): array
    {
        return $this->selectColumns;
    }

    public function getMapInstructor(): Mappable
    {
        return $this->instructor;
    }

    function getBindHint()
    {
        return $this->bindHint;
    }

    public function collect(array $fetchResults, array &$mapped)
    {
        $this->executor->exec($this, $fetchResults, $mapped);
    }
}
