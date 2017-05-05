<?php

namespace Rad\Db\Integration;

use PDO;
use Rad\Db\QueryBuildingDb;
use Rad\Db\Db;
use Rad\Db\Resources\JsonObject;
use Rad\Db\Resources\Book;

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
        $data->title = 'val';
        $data->pagecount = 567;
        // Execute
        $insertId = $this->queryBuildingDb->insert('books', $data);
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $this->assertEquals(
            [
                'id' => $insertId,
                'title' => $data->title,
                'pagecount' => $data->pagecount
            ],
            $this->fetchTestData($insertId)
        );
    }

    public function testInsertManyWritesultipleItemsToDb()
    {
        $data = new JsonObject();
        $data->title = 'val';
        $data2 = new JsonObject();
        $data2->title = 'another';
        // Execute
        $insertId = $this->queryBuildingDb->insertMany(
            'books',
            [$data, $data2]
        );
        // Assert
        $this->assertGreaterThan(0, $insertId);
        $insertedRows = $this->fetchTestData(null, 'fetchAll');
        $this->assertCount(2, $insertedRows);
        $insertedValues = array_column($insertedRows, 'title');
        $this->assertContains(
            $data->jsonSerialize()['title'],
            $insertedValues
        );
        $this->assertContains(
            $data2->jsonSerialize()['title'],
            $insertedValues
        );
    }

    public function testSelectAllUsesFetchArgs()
    {
        $entityClass = Book::class;
        $testRow = ['title' => 'ert'];
        $this->insertTestData($testRow);
        // Execute
        $results = $this->queryBuildingDb->selectAll(
            'books',
            ['id', 'title'],
            null, // filterApplier
            [PDO::FETCH_CLASS, $entityClass]
        );
        // Assert
        $this->assertInstanceOf($entityClass, $results[0]);
        $this->assertEquals($testRow['title'], $results[0]->getTitle());
    }

    public function testSelectOneReturnsEmptyArrayWhenConnectionReturnsEmptyResults()
    {
        // Execute
        $results = $this->queryBuildingDb->selectOne(
            'books',
            ['id', 'title']
        );
        // Assert
        $this->assertEquals([], $results);
    }

    public function testUpdateWithoutFilterApplierOverwritesData()
    {
        $insertId = $this->insertTestData(['title' => 'val']);
        // Execute
        $newData = new JsonObject();
        $newData->title = 'updated val';
        $result = $this->queryBuildingDb->update('books', $newData);
        // Assert
        $expectedRowCount = 1;
        $this->assertEquals($expectedRowCount, $result);
        $this->assertEquals(
            $newData->jsonSerialize()['title'],
            $this->fetchTestData($insertId)['title']
        );
    }

    public function testUpdateWithFilterApplierOverwritesData()
    {
        $firstData = ['title' => 'val'];
        $firstId = $this->insertTestData($firstData);
        $secondData = ['title' => 'val'];
        $secondId = $this->insertTestData($secondData);
        // Update only the second one
        $newData = new JsonObject();
        $newData->title = 'updated val';
        $filterApplier = function ($updateQueryRef) use ($secondId) {
            $updateQueryRef->where('id = :id');
            $updateQueryRef->bindValue('id', $secondId);
        };
        $result = $this->queryBuildingDb->update(
            'books',
            $newData,
            $filterApplier
        );
        // Assert
        $expectedRowCount = 1;
        $this->assertEquals($expectedRowCount, $result);
        $newFirstData = $this->fetchTestData($firstId);
        $newSecondData = $this->fetchTestData($secondId);
        $this->assertEquals(
            $newData->jsonSerialize()['title'],
            $newSecondData['title']
        );
        $this->assertEquals(
            $firstData['title'],
            $newFirstData['title']
        );
    }

    public function testDeleteWipesData()
    {
        $testRow = ['title' => 'ert'];
        $insertId = $this->insertTestData($testRow);
        $rowCountBeforeDeletion = count($this->fetchTestData(null, 'fetchAll'));
        $this->assertGreaterThan(0, $rowCountBeforeDeletion);
        // Execute
        $filterApplier = function ($deleteQueryRef) use ($insertId) {
            $deleteQueryRef->where('id = :id');
            $deleteQueryRef->bindValue('id', $insertId);
        };
        $result = $this->queryBuildingDb->delete('books', $filterApplier);
        // Assert
        $expectedRowCount = 1;
        $this->assertEquals($expectedRowCount, $result);
        $this->assertEmpty($this->fetchTestData($insertId));
        $rowCountAfterDeletion = count($this->fetchTestData(null, 'fetchAll'));
        $this->assertEquals($rowCountBeforeDeletion - 1, $rowCountAfterDeletion);
    }
}
