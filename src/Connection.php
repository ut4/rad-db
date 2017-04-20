<?php

namespace Rad\Db;

use PDO;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Common\DeleteInterface;

class Connection
{
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param InsertInterface $insertQuery
     * @return int
     */
    public function insert(InsertInterface $insertQuery): int
    {
        var_dump($q->getStatement());
        var_dump($q->getBindValues());
        return 1;
    }

    public function fetchAll(SelectInterface $q): array
    {
        var_dump($q->getStatement());
        var_dump($q->getBindValues());
        return [];
    }

    public function update(UpdateInterface $q): int
    {
        var_dump($q->getStatement());
        var_dump($q->getBindValues());
        return 1;
    }

    public function delete(DeleteInterface $q): int
    {
        var_dump($q->getStatement());
        var_dump($q->getBindValues());
        return 1;
    }
}
