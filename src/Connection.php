<?php

namespace Rad\Db;

use PDO;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Common\DeleteInterface;

class Connection
{
    protected $pdo;
    protected $fetchMode;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->fetchMode = PDO::FETCH_ASSOC;
    }

    /**
     * @param InsertInterface $insertQuery
     * @return int
     */
    public function insert(InsertInterface $insertQuery): int
    {
        $pdoStatement = $this->pdo->prepare($insertQuery->getStatement());
        $pdoStatement->execute($insertQuery->getBindValues());
        $this->rowCount = $pdoStatement->rowCount();
        return $this->rowCount > 0 ? (int) $this->lastInsertId() : 0;
    }

    /**
     * @param SelectInterface $selectQuery
     * @return array[]|array
     */
    public function fetchAll(SelectInterface $selectQuery): array
    {
        $pdoStatement = $this->pdo->prepare($selectQuery->getStatement());
        $pdoStatement->execute($selectQuery->getBindValues());
        return $pdoStatement->fetchAll($this->fetchMode);
    }

    /**
     * @param UpdateInterface $updateQuery
     * @return int
     */
    public function update(UpdateInterface $updateQuery): int
    {
        $pdoStatement = $this->pdo->prepare($updateQuery->getStatement());
        $pdoStatement->execute($updateQuery->getBindValues());
        return $pdoStatement->rowCount();
    }

    public function delete(DeleteInterface $q): int
    {
        var_dump($q->getStatement());
        var_dump($q->getBindValues());
        return 1;
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
