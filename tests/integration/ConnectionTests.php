<?php

namespace Rad\Db\Integration;

/**
 * Tests that Connection->pdo plays along with Aura\SqlQuery\QueryInterfaces
 */
class ConnectionTests extends InMemoryPDOTestCase
{
    /**
     * @before
     */
    public function beforeEach()
    {
        parent::beforeEach();
    }

    public function testInsertWritesDataToDb()
    {
        $expectedData = ['somecol' => 'a value', 'number' => 27];
        // Execute
        $insertId = $this->insertTestData($expectedData);
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $this->assertEquals(
            [
                'id' => $insertId,
                'somecol' => $expectedData['somecol'],
                'number' => $expectedData['number']
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
}
