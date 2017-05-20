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
        $setupQueries = $this->getTestDbSchemaQueries(
            func_num_args() > 0 ? func_get_arg(0) : false
        );
        array_map([$this->pdo, 'query'], $setupQueries);
        $this->connection = new Connection($this->pdo);
        $this->queryFactory = new QueryFactory('sqlite');
    }

    /**
     * Inserts $data to $tableName, example $data = ['key' => 'val', 'key2' => 'val2']
     */
    protected function insertTestData(
        array $data,
        string $tableName = 'books'
    ): int {
        $insertQuery = $this->queryFactory->newInsert();
        $insertQuery->into($tableName)->cols($data);
        return $this->connection->insert($insertQuery);
    }

    /**
     * Fetches data from $tableName where id = $whereExprOrId or ... $whereExprOrId using PDOStatement->$method
     */
    protected function fetchTestData(
        string $tableName,
        string $whereExprOrId = null,
        array $bind = null,
        string $method = 'fetch'
    ): array {
        if ($tableName === 'notes') {
            $q = 'SELECT id, content, booksId FROM notes';
        } else {
            $q = 'SELECT id, title, pagecount FROM books';
        }
        if (!$bind && $whereExprOrId) {
            $sth = $this->pdo->prepare($q . ' WHERE id = :id');
            $sth->execute(['id' => $whereExprOrId]);
        } else if($bind && $whereExprOrId) {
            $sth = $this->pdo->prepare($q . ' WHERE ' . $whereExprOrId);
            $sth->execute($bind);
        } else {
            $sth = $this->pdo->prepare($q);
            $sth->execute();
        }
        $rows = $sth->$method(PDO::FETCH_ASSOC);
        return $rows ? $rows : [];
    }

    private function getTestDbSchemaQueries(bool $full): array
    {
        if (!$full) {
            return ['CREATE TABLE books (' .
                'id INTEGER PRIMARY KEY AUTOINCREMENT, ' .
                'title TEXT, ' .
                'pagecount INTEGER' .
            ')'];
        }
        return [
            'CREATE TABLE books (' .
                'id INTEGER PRIMARY KEY AUTOINCREMENT, ' .
                'title TEXT, ' .
                'pagecount INTEGER, ' .
                'author_id INTEGER, ' .
                'FOREIGN KEY (author_id) REFERENCES authors(id)' .
            ')',
            'CREATE TABLE notes (' .
                'id INTEGER PRIMARY KEY AUTOINCREMENT, ' .
                'content TEXT,' .
                'booksId INTEGER, ' .
                'FOREIGN KEY (booksId) REFERENCES books(id)' .
            ')'
        ];
    }
}
