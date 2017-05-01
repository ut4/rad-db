<?php

namespace Rad\Db\Integration;

use Rad\Db\Resources\TestTableEntity;
use Aura\SqlQuery\QueryInterface;

trait BasicCrudRepositorySelectTests
{
    private $expectedDefaultColumns = ['id', 'somecol', 'number'];

    public function testSelectAllFetchesAndMapsAllDbRows()
    {
        $data = [
            ['somecol' => 'foo', 'number' => '23'],
            ['somecol' => 'bar']
        ];
        $id1 = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Execute
        $results = $this->testBasicCrudRepository->selectAll();
        // Assert
        $this->assertCount(2, $results);
        $this->assertInstanceOf(TestTableEntity::class, $results[0]);
        $this->assertInstanceOf(TestTableEntity::class, $results[1]);
        $this->assertEquals($this->expectedDefaultColumns, array_keys($results[0]->jsonSerialize()));
        $this->assertEquals($this->expectedDefaultColumns, array_keys($results[1]->jsonSerialize()));
        $this->assertSame($id1, $results[0]->getId());
        $this->assertSame($id2, $results[1]->getId());
        $this->assertSame($data[0]['somecol'], $results[0]->getSomecol());
        $this->assertSame($data[1]['somecol'], $results[1]->getSomecol());
        $this->assertSame((int) $data[0]['number'], $results[0]->getNumber());
        $this->assertSame(null, $results[1]->getNumber());
    }

    public function testSelectAllWithColumnListMapsOnlySelectedColumns()
    {
        $data = [
            ['somecol' => 'a', 'number' => '23'],
            ['somecol' => 'b', 'number' => '24']
        ];
        $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        $colsToSelect = ['somecol'];
        // Execute
        $results = $this->testBasicCrudRepository->selectAll($colsToSelect);
        // Assert
        $this->assertCount(2, $results);
        $this->assertInstanceOf(TestTableEntity::class, $results[0]);
        $this->assertInstanceOf(TestTableEntity::class, $results[1]);
        $this->assertEquals($colsToSelect, array_keys($results[0]->jsonSerialize()));
        $this->assertEquals($colsToSelect, array_keys($results[1]->jsonSerialize()));
        $this->assertSame($data[0]['somecol'], $results[0]->getSomecol());
        $this->assertSame($data[1]['somecol'], $results[1]->getSomecol());
        $this->assertSame(null, $results[0]->getNumber());
        $this->assertSame(null, $results[1]->getNumber());
    }

    public function testFindAllFetchesAndMapsAllDbRowsMatchingFilters()
    {
        $data = [
            ['somecol' => 'foo', 'number' => '23'],
            ['somecol' => 'bar']
        ];
        $id = $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        // Execute
        $results = $this->testBasicCrudRepository->findAll(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            }
        );
        // Assert
        $this->assertCount(1, $results);
        $this->assertInstanceOf(TestTableEntity::class, $results[0]);
        $this->assertEquals($this->expectedDefaultColumns, array_keys($results[0]->jsonSerialize()));
        $this->assertSame($id, $results[0]->getId());
        $this->assertSame($data[0]['somecol'], $results[0]->getSomecol());
        $this->assertSame((int) $data[0]['number'], $results[0]->getNumber());
    }

    public function testFindAllWithColumnListMapsOnlySelectedColumns()
    {
        $data = [
            ['somecol' => 'foo', 'number' => '23'],
            ['somecol' => 'bar', 'number' => '24']
        ];
        $id = $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        $colsToSelect = ['somecol'];
        // Execute
        $results = $this->testBasicCrudRepository->findAll(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            },
            $colsToSelect
        );
        // Assert
        $this->assertCount(1, $results);
        $this->assertInstanceOf(TestTableEntity::class, $results[0]);
        $this->assertEquals($colsToSelect, array_keys($results[0]->jsonSerialize()));
        $this->assertSame($data[0]['somecol'], $results[0]->getSomecol());
        $this->assertSame(null, $results[0]->getNumber());
    }

    public function testFindOneFetchesAndMapsSingleDbRowsMatchingFilters()
    {
        $data = [
            ['somecol' => 'foo', 'number' => '23'],
            ['somecol' => 'bar']
        ];
        $id = $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        // Execute
        $result = $this->testBasicCrudRepository->findOne(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            }
        );
        // Assert
        $this->assertInstanceOf(TestTableEntity::class, $result);
        $this->assertEquals($this->expectedDefaultColumns, array_keys($result->jsonSerialize()));
        $this->assertSame($id, $result->getId());
        $this->assertSame($data[0]['somecol'], $result->getSomecol());
        $this->assertSame((int) $data[0]['number'], $result->getNumber());
    }

    public function testFetchOneWithColumnListMapsOnlySelectedColumns()
    {
        $data = ['somecol' => 'foo', 'number' => '23'];
        $colsToSelect = ['id', 'somecol'];
        $id = $this->insertTestData($data);
        // Execute
        $result = $this->testBasicCrudRepository->findOne(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            },
            $colsToSelect
        );
        // Assert
        $this->assertInstanceOf(TestTableEntity::class, $result);
        $this->assertEquals($colsToSelect, array_keys($result->jsonSerialize()));
        $this->assertSame($id, $result->getId());
        $this->assertSame($data['somecol'], $result->getSomecol());
        $this->assertSame(null, $result->getNumber());
    }
}
