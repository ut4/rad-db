<?php

namespace Rad\Db;

use JsonSerializable;
use Aura\SqlQuery\QueryInterface;

abstract class BasicCrudRepository
{
    /**
     * @var QueryBuildingDb
     */
    private $queryBuildingDb;
    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @param QueryBuildingDb $queryBuildingDb
     * @param Mapper $mapper = null
     */
    public function __construct(
        QueryBuildingDb $queryBuildingDb,
        Mapper $mapper = null
    ) {
        $this->queryBuildingDb = $queryBuildingDb;
        $this->mapper = $mapper ?? new BasicMapper($this->getEntityClassPath());
    }

    /**
     * @return string
     */
    public abstract function getTableName(): string;

    /**
     * @return string
     */
    public function getIdColumnName(): string
    {
        return 'id';
    }

    /**
     * @return string
     */
    public abstract function getEntityClassPath(): string;

    /**
     * @param array[] $data
     * @param array $bindHints = null
     * @return int
     */
    public function insert(array $data, array $bindHints = null): int
    {
        return $this->queryBuildingDb->insert(
            $this->getTableName(),
            $this->mapper->mapAll($data, [$this->getIdColumnName()], $bindHints)
        );
    }

    /**
     * @param array $cols = null
     * @return JsonSerializable[]
     */
    public function selectAll(array $cols = null): array
    {
        return $this->doSelect(true, $cols);
    }

    /**
     * @param Callable $filterApplier
     * @param array $cols = null
     * @return JsonSerializable[]
     */
    public function findAll(Callable $filterApplier, array $cols = null): array
    {
        return $this->doSelect(true, $cols, $filterApplier);
    }

    /**
     * @param Callable $filterApplier
     * @param array $cols = null
     * @return JsonSerializable
     */
    public function findOne(Callable $filterApplier, array $cols = null): JsonSerializable
    {
        return $this->doSelect(false, $cols, $filterApplier);
    }

    /**
     * @param bool $selectMany
     * @param array $cols = null
     * @param Callable $filterApplier = null
     * @return JsonSerializable|JsonSerializable[]
     */
    private function doSelect(
        bool $selectMany,
        array $cols = null,
        Callable $filterApplier = null
    ) {
        return $this->mapper->{$selectMany ? 'mapAll' : 'map'}(
            $this->queryBuildingDb->{$selectMany ? 'selectAll' : 'selectOne'}(
                $this->getTableName(),
                $cols ?? array_keys($this->mapper->map([], null, null, true)->jsonSerialize()),
                $filterApplier
            )
        );
    }

    /**
     * @param array $input
     * @param Callable $filterApplier = null
     * @param array $bindHints = null
     * @return int
     */
    public function update(
        array $input,
        Callable $filterApplier = null,
        array $bindHints = null
    ): int {
        if (!$filterApplier) {
            $filterApplier = $this->makeDefaultWhere($input);
        }
        return $this->queryBuildingDb->update(
            $this->getTableName(),
            $this->mapper->map($input, [$this->getIdColumnName()], $bindHints),
            $filterApplier
        );
    }

    /**
     * @param array $input
     * @param Callable $filterApplier = null
     * @return int
     */
    public function delete(array $input, Callable $filterApplier = null): int
    {
        if (!$filterApplier) {
            $filterApplier = $this->makeDefaultWhere($input);
        }
        return $this->queryBuildingDb->delete(
            $this->getTableName(),
            $filterApplier
        );
    }

    /**
     * @param array $input
     * @return Callable
     */
    private function makeDefaultWhere(array $input): Callable
    {
        return function (QueryInterface $q) use ($input) {
            $idCol = $this->getIdColumnName();
            $q->where($idCol . ' = :idVal');
            $q->bindValue(
                'idVal',
                isset($input[$idCol]) ? (int) $input[$idCol] : null
            );
        };
    }
}
