<?php

namespace Rad\Db;

use JsonSerializable;
use InvalidArgumentException;

abstract class AutoRepository implements Repository
{
    private $hintQueryRunner;
    private $queryBuildingDb;
    private $mapper;
    private $rootMapInstructor;

    /**
     * @param HintQueryRunner $hintQueryRunner
     * @param QueryBuildingDb $queryBuildingDb
     * @param Mapper $mapper = null
     */
    public function __construct(
        HintQueryRunner $hintQueryRunner,
        QueryBuildingDb $queryBuildingDb,
        Mapper $mapper = null
    ) {
        $this->hintQueryRunner = $hintQueryRunner;
        $this->queryBuildingDb = $queryBuildingDb;
        $instructorClassPath = $this->getMapInstructorClassPath();
        if (!\is_subclass_of($instructorClassPath, Mappable::class)) {
            throw new InvalidArgumentException(
                $instructorClassPath . ' should implement \\Rad\\Db\\Mappable'
            );
        }
        $this->rootMapInstructor = new $instructorClassPath();
        $this->mapper = $mapper ?? new BasicMapper($this->rootMapInstructor->getEntityClassPath());
    }

    public abstract function getMapInstructorClassPath(): string;

    /**
     * @param array|array[] $data
     * @param BindHint[] $bindHints = null
     * @return int
     */
    public function insert(array $data, array $bindHints = null): int
    {
        $mainQ = new QueryPacket($data, $this->rootMapInstructor);
        if (($mainResult = $this->execInsert($mainQ)) < 1) {
            return $mainResult;
        }
        $mainQ->setResult($mainResult);
        if ($this->rootMapInstructor->getBindHints()) {
            $result = $this->hintQueryRunner->run($mainQ, [$this, 'execInsert']);
            if ($result < 1) {
                return $result;
            }
        }
        return $mainResult;
    }

    /**
     * @param QueryPacket $insertQuery
     * @return int
     */
    public function execInsert(QueryPacket $insertQuery): int
    {
        $instructions = $insertQuery->getMapInstructor();
        return $this->queryBuildingDb->insertMany(
            $instructions->getTableName(),
            $this->mapper->mapAll(
                isset($insertQuery->getData()[0])
                    ? $insertQuery->getData()
                    : [$insertQuery->getData()],
                [$instructions->getIdColumnName()],
                $instructions->getEntityClassPath()
            )
        );
    }

    public function findAll(Callable $filterApplier, array $cols = null): array
    {
        throw new \Exception('Not implemented');
    }

    public function findOne(
        Callable $filterApplier,
        array $cols = null
    ): JsonSerializable {
        throw new \Exception('Not implemented');
    }

    public function selectAll(array $cols = null): array
    {
        throw new \Exception('Not implemented');
    }

    public function delete(array $input): int
    {
        throw new \Exception('Not implemented');
    }

    public function update(array $input): int
    {
        throw new \Exception('Not implemented');
    }
}
