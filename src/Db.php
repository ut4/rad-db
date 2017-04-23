<?php

namespace Rad\Db;

use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Common\DeleteInterface;

class Db
{
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param InsertInterface $insertQuery
     * @return int
     */
    public function insert(InsertInterface $insertQuery): int
    {
        return $this->conn->insert($insertQuery);
    }

    /**
     * @param SelectInterface $selectQuery
     * @param $fetchArgs = null
     * @return array[]
     */
    public function selectAll(
        SelectInterface $selectQuery,
        $fetchArgs = null
    ): array {
        return $this->conn->fetchAll($selectQuery, $fetchArgs);
    }

    /**
     * @param SelectInterface $selectQuery
     * @param $fetchArgs = null
     * @return array
     */
    public function selectOne(
        SelectInterface $selectQuery,
        $fetchArgs = null
    ): array {
        return $this->selectAll($selectQuery, $fetchArgs)[0] ?? [];
    }

    /**
     * @param UpdateInterface $updateQuery
     * @return int
     */
    public function update(UpdateInterface $updateQuery): int
    {
        return $this->conn->update($updateQuery);
    }

    /**
     * @param DeleteInterface $deleteQuery
     * @return int
     */
    public function delete(DeleteInterface $deleteQuery): int
    {
        return $this->conn->delete($deleteQuery);
    }
}
