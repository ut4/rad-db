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
        $expectedData = ['title' => 'a value', 'pagecount' => 27];
        // Execute
        $insertId = $this->insertTestData($expectedData);
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $this->assertEquals(
            [
                'id' => $insertId,
                'title' => $expectedData['title'],
                'pagecount' => $expectedData['pagecount']
            ],
            $this->fetchTestData('books', $insertId)
        );
    }

    public function testFetchAllReadsData()
    {
        $rows = [['title' => 'a'], ['title' => 'b']];
        $this->insertTestData($rows[0]);
        $this->insertTestData($rows[1]);
        $selectQuery = $this->queryFactory->newSelect();
        $selectQuery->from('books')->cols(['id', 'title']);
        // Execute
        $results = $this->connection->fetchAll($selectQuery);
        // Assert
        $this->assertEquals($rows[0]['title'], $results[0]['title']);
        $this->assertEquals($rows[1]['title'], $results[1]['title']);
    }

    public function testUpdateOverwritesData()
    {
        $rows = [['title' => 'c'], ['title' => 'd']];
        $id = $this->insertTestData($rows[0]);
        $updateQuery = $this->queryFactory->newUpdate();
        $updateQuery->table('books')
            ->cols(['title'])
            ->where('id = :id')
            ->bindValues([
                'id' => $id,
                'title' => $rows[1]['title']
            ]);
        // Execute
        $updateRowCount = $this->connection->update($updateQuery);
        // Assert
        $this->assertEquals(1, $updateRowCount);
        $this->assertEquals($rows[1]['title'], $rows[1]['title']);
    }

    public function testDeleteWipesData()
    {
        $someData = ['title' => 'e'];
        $id = $this->insertTestData($someData);
        $countBeforeDeletion = count($this->fetchTestData('books', $id, null, 'fetchAll'));
        $deleteQuery = $this->queryFactory->newDelete();
        $deleteQuery->from('books')->where('id = :id')->bindValue('id', $id);
        // Execute
        $deleteRowCount = $this->connection->delete($deleteQuery);
        // Assert
        $this->assertEquals(1, $deleteRowCount);
        $countAfterDeletion = count($this->fetchTestData('books', $id, null, 'fetchAll'));
        $this->assertEquals($countBeforeDeletion - 1, $countAfterDeletion);
    }
}
