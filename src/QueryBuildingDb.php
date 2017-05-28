<?php

namespace Rad\Db;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Common\DeleteInterface;
use JsonSerializable;
use UnexpectedValueException;

/**
 * A Db decorator, allows $db->insert('sometable', $data) instead of
 * $insertQ = $queryFactory->newInsert() ... $db->insert($insertQ) etc.
 */
class QueryBuildingDb
{
    protected $db;
    protected $queryFactory;

    public function __construct(Db $db, QueryFactory $queryFactory)
    {
        $this->db = $db;
        $this->queryFactory = $queryFactory;
    }

    /**
     * @param string|Aura\SqlQuery\Common\InsertInterface $tableNameOrQuery
     * @param JsonSerializable $mappedData = null
     * @return int
     */
    public function insert(
        $tableNameOrQuery,
        JsonSerializable $mappedData = null
    ): int {
        return $this->db->insert(
            ($tableNameOrQuery instanceof InsertInterface)
                ? $tableNameOrQuery
                : $this->makeInsertQuery($tableNameOrQuery, $mappedData)
        );
    }

    /**
     * @param string|Aura\SqlQuery\Common\InsertInterface $tableNameOrQuery
     * @param JsonSerializable[] $mappedData = null
     * @return int
     */
    public function insertMany($tableNameOrQuery, array $mappedData = null): int
    {
        return $this->db->insert(
            ($tableNameOrQuery instanceof InsertInterface)
                ? $tableNameOrQuery
                : $this->makeInsertManyQuery($tableNameOrQuery, $mappedData)
        );
    }

    /**
     * @param string $tableName
     * @param JsonSerializable $mappedData
     * @return InsertInterface
     */
    private function makeInsertQuery(
        string $tableName,
        JsonSerializable $mappedData
    ): InsertInterface {
        $insert = $this->queryFactory->newInsert();
        $insert->into($tableName);
        $insert->cols($this->jsonSerializeRecursively($mappedData));
        return $insert;
    }

    /**
     * @param string $tableName
     * @param JsonSerializable[] $mappedData
     * @return InsertInterface
     */
    private function makeInsertManyQuery(
        string $tableName,
        array $mappedData
    ): InsertInterface {
        $values = $this->jsonSerializeAll($mappedData);
        $insert = $this->queryFactory->newInsert();
        $insert->into($tableName);
        $insert->cols($values[0]);
        if (count($values) > 1) {
            $insert->addRows(array_slice($values, 1));
        }
        return $insert;
    }

    /**
     * @param string|Aura\SqlQuery\Common\SelectInterface $tableNameOrQuery
     * @param array $columns = null
     * @param Closure $filterApplier = null
     * @param $fetchArgs = null
     * @param array[]
     */
    public function selectAll(
        $tableNameOrQuery,
        array $columns = null,
        Closure $filterApplier = null,
        array $fetchArgs = null
    ): array {
        return $this->doSelect(
            'selectAll',
            $tableNameOrQuery,
            $columns,
            $filterApplier,
            $fetchArgs
        );
    }

    /**
     * @param string|Aura\SqlQuery\Common\SelectInterface $tableNameOrQuery
     * @param array $columns
     * @param Closure $filterApplier = null
     * @param $fetchArgs = null
     * @param array
     */
    public function selectOne(
        $tableNameOrQuery,
        array $columns = null,
        Closure $filterApplier = null,
        array $fetchArgs = null
    ): array {
        return $this->doSelect(
            'selectOne',
            $tableNameOrQuery,
            $columns,
            $filterApplier,
            $fetchArgs
        );
    }

    /**
     * @param string $method selectAll or selectOne
     * @param string|Aura\SqlQuery\Common\SelectInterface $tableNameOrQuery
     * @param array $columns
     * @param Closure $filterApplier = null
     * @param $fetchArgs = null
     * @param array
     */
    private function doSelect(
        string $method,
        $tableNameOrQuery,
        array $columns = null,
        Closure $filterApplier = null,
        array $fetchArgs = null
    ): array {
        return $this->db->$method(
            ($tableNameOrQuery instanceof SelectInterface)
                ? $tableNameOrQuery
                : $this->makeSelectQuery(
                    $tableNameOrQuery,
                    $columns,
                    $filterApplier
                ),
            $fetchArgs
        );
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @param Closure $filterApplier = null
     * @param SelectInterface
     */
    private function makeSelectQuery(
        string $tableName,
        array $columns,
        Closure $filterApplier = null
    ): SelectInterface {
        $select = $this->queryFactory->newSelect();
        $select->from($tableName);
        $select->cols($columns);
        if ($filterApplier) {
            $filterApplier($select);
        }
        return $select;
    }

    /**
     * @param string|Aura\SqlQuery\Common\UpdateInterface $tableNameOrQuery
     * @param JsonSerializable $mappedData = null
     * @param Closure $filterApplier = null
     * @return int
     */
    public function update(
        $tableNameOrQuery,
        JsonSerializable $mappedData = null,
        Closure $filterApplier = null
    ): int {
        return $this->db->update(
            ($tableNameOrQuery instanceof UpdateInterface)
                ? $tableNameOrQuery
                : $this->makeUpdateQuery(
                    $tableNameOrQuery,
                    $mappedData,
                    $filterApplier
                )
        );
    }

    /**
     * @param string $tableName
     * @param JsonSerializable $mappedData
     * @param Closure $filterApplier = null
     * @return UpdateInterface
     */
    private function makeUpdateQuery(
        string $tableName,
        JsonSerializable $mappedData,
        Closure $filterApplier = null
    ): UpdateInterface {
        $value = $this->jsonSerializeRecursively($mappedData);
        $update = $this->queryFactory->newUpdate();
        $update->table($tableName);
        $update->cols(array_keys($value));
        $update->bindValues($value);
        if ($filterApplier) {
            $filterApplier($update);
        }
        return $update;
    }

    /**
     * @param string|Aura\SqlQuery\Common\DeleteInterface $tableNameOrQuery
     * @param Closure $filterApplier = null
     * @return int
     */
    public function delete(
        $tableNameOrQuery,
        Closure $filterApplier = null
    ): int {
        return $this->db->delete(
            ($tableNameOrQuery instanceof DeleteInterface)
                ? $tableNameOrQuery
                : $this->makeDeleteQuery($tableNameOrQuery, $filterApplier)
        );
    }

    /**
     * @return QueryFactory
     */
    public function getQueryFactory(): QueryFactory
    {
        return $this->queryFactory;
    }

    /**
     * @param string $tableName
     * @param Closure $filterApplier
     * @return DeleteInterface
     */
    private function makeDeleteQuery(
        string $tableName,
        Closure $filterApplier
    ): DeleteInterface {
        $delete = $this->queryFactory->newDelete();
        $delete->from($tableName);
        $filterApplier($delete);
        return $delete;
    }

    /**
     * @param JsonSerializable[] $mappedData
     * @return array[]
     * @throws UnexpectedValueException
     */
    private function jsonSerializeAll(array $mappedData): array
    {
        foreach ($mappedData as $item) {
            if (!($item instanceof JsonSerializable)) {
                throw new UnexpectedValueException(
                    'All items of $mappedData should implement ' .
                    '\\JsonSerializable'
                );
            }
        }
        return $this->jsonSerializeRecursively($mappedData);
    }

    /**
     * jsonSerialize's all $mappedData values recursively (because
     * $mapped->jsonSerialize() doesn't do that).
     *
     * @param JsonSerializable|JsonSerializable[] $mappedData
     * @return array[] The encoded result
     */
    private function jsonSerializeRecursively($mappedData)
    {
        return json_decode(json_encode($mappedData), true);
    }
}
