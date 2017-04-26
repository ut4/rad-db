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
     * @param JsonSerializable[] $mappedData = null
     * @return int
     */
    public function insert($tableNameOrQuery, array $mappedData = null): int
    {
        return $this->db->insert(
            ($tableNameOrQuery instanceof InsertInterface)
                ? $tableNameOrQuery
                : $this->makeInsertQuery($tableNameOrQuery, $mappedData)
        );
    }

    /**
     * @param string $tableName
     * @param JsonSerializable[] $mappedData
     * @return InsertInterface
     */
    private function makeInsertQuery(
        string $tableName,
        array $mappedData
    ): InsertInterface {
        $values = $this->getMappedDataAsAssoc($mappedData);
        $insert = $this->queryFactory->newInsert()
            ->into($tableName)
            ->cols($values[0]);
        if (count($values) > 1) {
            $insert->addRows(array_slice($values, 1));
        }
        return $insert;
    }

    /**
     * @param string|Aura\SqlQuery\Common\SelectInterface $tableNameOrQuery
     * @param array $columns = null
     * @param $fetchArgs = null
     * @param Callable $filterApplier = null
     * @param array[]
     */
    public function selectAll(
        $tableNameOrQuery,
        array $columns = null,
        array $fetchArgs = null,
        Callable $filterApplier = null
    ): array {
        return $this->db->selectAll(
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
     * @param Callable $filterApplier = null
     * @param SelectInterface
     */
    private function makeSelectQuery(
        string $tableName,
        array $columns,
        Callable $filterApplier = null
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
     * @param string|Aura\SqlQuery\Common\SelectInterface $tableNameOrQuery
     * @param array $columns
     * @param Callable $filterApplier = null
     * @param array|null
     */
    public function selectOne(
        $tableNameOrQuery,
        array $columns = null,
        Callable $filterApplier = null
    ): array {
        return $this->selectAll(
            $tableNameOrQuery,
            $columns,
            $filterApplier
        )[0] ?? [];
    }

    /**
     * @param string|Aura\SqlQuery\Common\UpdateInterface $tableNameOrQuery
     * @param JsonSerializable $mappedData = null
     * @param Callable $filterApplier = null
     * @return int
     */
    public function update(
        $tableNameOrQuery,
        JsonSerializable $mappedData = null,
        Callable $filterApplier = null
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
     * @param Callable $filterApplier = null
     * @return UpdateInterface
     */
    private function makeUpdateQuery(
        string $tableName,
        JsonSerializable $mappedData,
        Callable $filterApplier = null
    ): UpdateInterface {
        $value = $mappedData->jsonSerialize();
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
     * @param Callable $filterApplier = null
     * @return int
     */
    public function delete(
        $tableNameOrQuery,
        Callable $filterApplier = null
    ): int {
        return $this->db->delete(
            ($tableNameOrQuery instanceof DeleteInterface)
                ? $tableNameOrQuery
                : $this->makeDeleteQuery($tableNameOrQuery, $filterApplier)
        );
    }

    /**
     * @param string $tableName
     * @param Callable $filterApplier
     * @return DeleteInterface
     */
    private function makeDeleteQuery(
        string $tableName,
        Callable $filterApplier
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
    private function getMappedDataAsAssoc(array $mappedData): array
    {
        $asAssoc = [];
        foreach ($mappedData as $item) {
            if (!($item instanceof JsonSerializable)) {
                throw new UnexpectedValueException(
                    'All items of $mappedData should implement ' .
                    '\\JsonSerializable'
                );
            }
            $asAssoc[] = $item->jsonSerialize();
        }
        return $asAssoc;
    }
}
