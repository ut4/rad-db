<?php

namespace Rad\Db\Integration;

use PDO;
use Rad\Db\Connection;
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
        $this->pdo->query('CREATE TABLE books (' .
            'id INTEGER PRIMARY KEY AUTOINCREMENT, ' .
            'title TEXT, ' .
            'pagecount INTEGER' .
        ')');
        $this->connection = new Connection($this->pdo);
        $this->queryFactory = new QueryFactory('sqlite');
    }

    /**
     * Inserts $data to books
     */
    protected function insertTestData(array $data): int
    {
        $insertQuery = $this->queryFactory->newInsert();
        $insertQuery->into('books')->cols($data);
        return $this->connection->insert($insertQuery);
    }

    /**
     * Fetches data from books where id = $id using PDOStatement->$method
     */
    protected function fetchTestData(
        string $id = null,
        string $method = 'fetch'
    ): array {
        $q = 'SELECT id, title, pagecount FROM books';
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
