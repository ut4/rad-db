<?php

namespace Rad\Db;

use PDO;
use Aura\SqlQuery\QueryFactory;
use PHPUnit\Framework\TestCase;

class InMemoryPDOTestCase extends TestCase
{
    protected $pdo;
    protected $connection;
    protected $queryFactory;

    public function beforeEach()
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->query('CREATE TABLE test_table (' .
            'id INTEGER PRIMARY KEY AUTOINCREMENT, ' .
            'somecol TEXT' .
        ')');
        $this->connection = new Connection($this->pdo);
        $this->queryFactory = new QueryFactory('sqlite');
    }

    /**
     * Inserts $data to test_table
     */
    protected function insertTestData(array $data): int
    {
        $insertQuery = $this->queryFactory->newInsert();
        $insertQuery->into('test_table')->cols($data);
        return $this->connection->insert($insertQuery);
    }

    /**
     * Fetches data from test_table where id = $id using PDOStatement->$method
     */
    protected function fetchTestData(
        string $id = null,
        string $method = 'fetch'
    ): array {
        $q = 'SELECT id, somecol FROM test_table';
        if ($id) {
            $sth = $this->pdo->prepare($q . ' WHERE id = :id');
            $sth->execute(['id' => $id]);
        } else {
            $sth = $this->pdo->prepare($q);
            $sth->execute();
        }
        $rows = $sth->$method(PDO::FETCH_ASSOC);
        return $rows ? $rows : [];
    }
}
