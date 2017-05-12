<?php

namespace Rad\Db;

use JsonSerializable;
use Aura\SqlQuery\QueryInterface;

abstract class BasicCrudRepository implements Repository
{
    /**
     * @var QueryBuildingDb
     */
    private $queryBuildingDb;
    /**
     * @var Mappable
     */
    private $mappingInstructor;
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
        $instructorClassPath = $this->getMapInstructorClassPath();
        if (!is_subclass_of($instructorClassPath, Mappable::class)) {
            throw new InvalidArgumentException(
                $instructorClassPath . ' should implement \\Rad\\Db\\Mappable'
            );
        }
        $this->mappingInstructor = new $instructorClassPath();
        $this->mapper = $mapper ?? new BasicMapper($this->mappingInstructor->getEntityClassPath());
    }

    public abstract function getMapInstructorClassPath(): string;

    /**
     * @param array|array[] $data
     * @param BindHint[] $bindHints = null
     * @return int
     */
    public function insert(array $data, array $bindHints = null): int
    {
        if (isset($data[0])) {
            return $this->insertMany($data, $bindHints);
        }
        return $this->queryBuildingDb->insert(
            $this->mappingInstructor->getTableName(),
            $this->mapper->map($data, [$this->mappingInstructor->getIdColumnName()], $bindHints)
        );
    }

    /**
     * @param array[] $data
     * @param array $bindHints = null
     * @return int
     */
    public function insertMany(array $data, array $bindHints = null): int
    {
        return $this->queryBuildingDb->insertMany(
            $this->mappingInstructor->getTableName(),
            $this->mapper->mapAll($data, [$this->mappingInstructor->getIdColumnName()])
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
    public function findAll(
        Callable $filterApplier,
        array $cols = null
    ): array {
        return $this->doSelect(true, $cols, $filterApplier);
    }

    /**
     * @param Callable $filterApplier
     * @param array $cols = null
     * @return JsonSerializable
     */
    public function findOne(
        Callable $filterApplier,
        array $cols = null
    ): JsonSerializable {
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
                $this->mappingInstructor->getTableName(),
                $cols ?? $this->mapper->getKeys(),
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
            $this->mappingInstructor->getTableName(),
            $this->mapper->map($input, [$this->mappingInstructor->getIdColumnName()]),
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
            $this->mappingInstructor->getTableName(),
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
            $idCol = $this->mappingInstructor->getIdColumnName();
            $q->where($idCol . ' = :idVal');
            $q->bindValue(
                'idVal',
                isset($input[$idCol]) ? (int) $input[$idCol] : null
            );
        };
    }
}
