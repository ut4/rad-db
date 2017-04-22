<?php

namespace Rad\Db;

use PDO;
use Aura\SqlQuery\QueryFactory;
use PHPUnit\Framework\TestCase;

/**
 * Tests that Connection->pdo plays along with Aura\SqlQuery\QueryInterfaces
 */
class ConnectionTests extends TestCase
{
    private $pdo;
    private $connection;
    private $queryFactory;

    /**
     * @before
     */
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

    public function testInsertWritesDataToDb()
    {
        $expectedData = ['somecol' => 'a value'];
        // Execute
        $insertId = $this->insertTestData($expectedData);
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $this->assertEquals(
            [
                'id' => $insertId,
                'somecol' => $expectedData['somecol']
            ],
            $this->fetchTestData($insertId)
        );
    }

    public function testFetchAllReadsData()
    {
        $rows = [['somecol' => 'a'], ['somecol' => 'b']];
        $this->insertTestData($rows[0]);
        $this->insertTestData($rows[1]);
        $selectQuery = $this->queryFactory->newSelect();
        $selectQuery->from('test_table')->cols(['id', 'somecol']);
        // Execute
        $results = $this->connection->fetchAll($selectQuery);
        // Assert
        $this->assertEquals($rows[0]['somecol'], $results[0]['somecol']);
        $this->assertEquals($rows[1]['somecol'], $results[1]['somecol']);
    }

    public function testUpdateOverwritesData()
    {
        $rows = [['somecol' => 'c'], ['somecol' => 'd']];
        $id = $this->insertTestData($rows[0]);
        $updateQuery = $this->queryFactory->newUpdate();
        $updateQuery->table('test_table')
            ->cols(['somecol'])
            ->where('id = :id')
            ->bindValues([
                'id' => $id,
                'somecol' => $rows[1]['somecol']
            ]);
        // Execute
        $updateRowCount = $this->connection->update($updateQuery);
        // Assert
        $this->assertEquals(1, $updateRowCount);
        $this->assertEquals($rows[1]['somecol'], $rows[1]['somecol']);
    }

    public function testDeleteWipesData()
    {
        $someData = ['somecol' => 'e'];
        $id = $this->insertTestData($someData);
        $countBeforeDeletion = count($this->fetchTestData($id, 'fetchAll'));
        $deleteQuery = $this->queryFactory->newDelete();
        $deleteQuery->from('test_table')->where('id = :id')->bindValue('id', $id);
        // Execute
        $deleteRowCount = $this->connection->delete($deleteQuery);
        // Assert
        $this->assertEquals(1, $deleteRowCount);
        $countAfterDeletion = count($this->fetchTestData($id, 'fetchAll'));
        $this->assertEquals($countBeforeDeletion - 1, $countAfterDeletion);
    }

    /**
     * Inserts some $data into test_table
     */
    private function insertTestData(array $data): int
    {
        $insertQuery = $this->queryFactory->newInsert();
        $insertQuery->into('test_table')->cols($data);
        return $this->connection->insert($insertQuery);
    }

    /**
     * Fetches data from test_table where id = $id using PDOStatement->$method
     */
    private function fetchTestData(string $id, string $method = 'fetch'): array
    {
        $sth = $this->pdo->prepare('SELECT id,somecol FROM test_table WHERE id = :id');
        $sth->execute(['id' => $id]);
        return $sth->$method(PDO::FETCH_ASSOC);
    }
}
