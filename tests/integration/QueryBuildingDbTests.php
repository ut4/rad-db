<?php

namespace Rad\Db\Integration;

use PDO;
use Rad\Db\QueryBuildingDb;
use Rad\Db\Db;
use Rad\Db\Resources\JsonObject;
use Rad\Db\Resources\TestTableEntity;

/**
 * Tests that QueryBuildingDb writes/selects stuff correctly from/to the
 * database.
 */
class QueryBuildingDbTests extends InMemoryPDOTestCase
{
    private $queryBuildingDb;

    /**
     * @before
     */
    public function beforeEach()
    {
        parent::beforeEach();
        $this->queryBuildingDb = new QueryBuildingDb(
            new Db($this->connection),
            $this->queryFactory
        );
    }

    public function testInsertWritesSingleItemToDb()
    {
        $data = new JsonObject();
        $data->somecol = 'val';
        $data->number = 567;
        // Execute
        $insertId = $this->queryBuildingDb->insert('test_table', $data);
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $this->assertEquals(
            [
                'id' => $insertId,
                'somecol' => $data->somecol,
                'number' => $data->number
            ],
            $this->fetchTestData($insertId)
        );
    }

    public function testInsertManyWritesultipleItemsToDb()
    {
        $data = new JsonObject();
        $data->somecol = 'val';
        $data2 = new JsonObject();
        $data2->somecol = 'another';
        // Execute
        $insertId = $this->queryBuildingDb->insertMany(
            'test_table',
            [$data, $data2]
        );
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $insertedRows = $this->fetchTestData(null, 'fetchAll');
        $this->assertCount(2, $insertedRows);
        $insertedValues = array_column($insertedRows, 'somecol');
        $this->assertContains(
            $data->jsonSerialize()['somecol'],
            $insertedValues
        );
        $this->assertContains(
            $data2->jsonSerialize()['somecol'],
            $insertedValues
        );
    }

    public function testSelectAllUsesFetchArgs()
    {
        $entityClass = TestTableEntity::class;
        $testRow = ['somecol' => 'ert'];
        $this->insertTestData($testRow);
        // Execute
        $results = $this->queryBuildingDb->selectAll(
            'test_table',
            ['id', 'somecol'],
            null, // filterApplier
            [PDO::FETCH_CLASS, $entityClass]
        );
        // Assert
        $this->assertInstanceOf($entityClass, $results[0]);
        $this->assertEquals($testRow['somecol'], $results[0]->getSomecol());
    }

    public function testSelectOneReturnsEmptyArrayWhenConnectionReturnsEmptyResults()
    {
        // Execute
        $results = $this->queryBuildingDb->selectOne(
            'test_table',
            ['id', 'somecol']
        );
        // Assert
        $this->assertEquals([], $results);
    }

    public function testUpdateWithoutFilterApplierOverwritesData()
    {
        $insertId = $this->insertTestData(['somecol' => 'val']);
        // Execute
        $newData = new JsonObject();
        $newData->somecol = 'updated val';
        $result = $this->queryBuildingDb->update('test_table', $newData);
        // Assert
        $expectedRowCount = 1;
        $this->assertEquals($expectedRowCount, $result);
        $this->assertEquals(
            $newData->jsonSerialize()['somecol'],
            $this->fetchTestData($insertId)['somecol']
        );
    }

    public function testUpdateWithFilterApplierOverwritesData()
    {
        $firstData = ['somecol' => 'val'];
        $firstId = $this->insertTestData($firstData);
        $secondData = ['somecol' => 'val'];
        $secondId = $this->insertTestData($secondData);
        // Update only the second one
        $newData = new JsonObject();
        $newData->somecol = 'updated val';
        $filterApplier = function ($updateQueryRef) use ($secondId) {
            $updateQueryRef->where('id = :id');
            $updateQueryRef->bindValue('id', $secondId);
        };
        $result = $this->queryBuildingDb->update(
            'test_table',
            $newData,
            $filterApplier
        );
        // Assert
        $expectedRowCount = 1;
        $this->assertEquals($expectedRowCount, $result);
        $newFirstData = $this->fetchTestData($firstId);
        $newSecondData = $this->fetchTestData($secondId);
        $this->assertEquals(
            $newData->jsonSerialize()['somecol'],
            $newSecondData['somecol']
        );
        $this->assertEquals(
            $firstData['somecol'],
            $newFirstData['somecol']
        );
    }

    public function testDeleteWipesData()
    {
        $testRow = ['somecol' => 'ert'];
        $insertId = $this->insertTestData($testRow);
        $rowCountBeforeDeletion = count($this->fetchTestData(null, 'fetchAll'));
        $this->assertGreaterThan(0, $rowCountBeforeDeletion);
        // Execute
        $filterApplier = function ($deleteQueryRef) use ($insertId) {
            $deleteQueryRef->where('id = :id');
            $deleteQueryRef->bindValue('id', $insertId);
        };
        $result = $this->queryBuildingDb->delete('test_table', $filterApplier);
        // Assert
        $expectedRowCount = 1;
        $this->assertEquals($expectedRowCount, $result);
        $this->assertEmpty($this->fetchTestData($insertId));
        $rowCountAfterDeletion = count($this->fetchTestData(null, 'fetchAll'));
        $this->assertEquals($rowCountBeforeDeletion - 1, $rowCountAfterDeletion);
    }
}
