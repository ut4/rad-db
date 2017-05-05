<?php

namespace Rad\Db\Integration;

use Rad\Db\Resources\Book;
use Aura\SqlQuery\QueryInterface;

trait BasicCrudRepositorySelectTests
{
    private $expectedDefaultColumns = ['id', 'title', 'pagecount'];

    public function testSelectAllFetchesAndMapsAllDbRows()
    {
        $data = [
            ['title' => 'foo', 'pagecount' => '23'],
            ['title' => 'bar']
        ];
        $id1 = $this->insertTestData($data[0]);
        $id2 = $this->insertTestData($data[1]);
        // Execute
        $results = $this->bookRepository->selectAll();
        // Assert
        $this->assertCount(2, $results);
        $this->assertInstanceOf(Book::class, $results[0]);
        $this->assertInstanceOf(Book::class, $results[1]);
        $this->assertEquals($this->expectedDefaultColumns, array_keys($results[0]->jsonSerialize()));
        $this->assertEquals($this->expectedDefaultColumns, array_keys($results[1]->jsonSerialize()));
        $this->assertSame($id1, $results[0]->getId());
        $this->assertSame($id2, $results[1]->getId());
        $this->assertSame($data[0]['title'], $results[0]->getTitle());
        $this->assertSame($data[1]['title'], $results[1]->getTitle());
        $this->assertSame((int) $data[0]['pagecount'], $results[0]->getPagecount());
        $this->assertSame(null, $results[1]->getPagecount());
    }

    public function testSelectAllWithColumnListMapsOnlySelectedColumns()
    {
        $data = [
            ['title' => 'a', 'pagecount' => '23'],
            ['title' => 'b', 'pagecount' => '24']
        ];
        $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        $colsToSelect = ['title'];
        // Execute
        $results = $this->bookRepository->selectAll($colsToSelect);
        // Assert
        $this->assertCount(2, $results);
        $this->assertInstanceOf(Book::class, $results[0]);
        $this->assertInstanceOf(Book::class, $results[1]);
        $this->assertEquals($colsToSelect, array_keys($results[0]->jsonSerialize()));
        $this->assertEquals($colsToSelect, array_keys($results[1]->jsonSerialize()));
        $this->assertSame($data[0]['title'], $results[0]->getTitle());
        $this->assertSame($data[1]['title'], $results[1]->getTitle());
        $this->assertSame(null, $results[0]->getPagecount());
        $this->assertSame(null, $results[1]->getPagecount());
    }

    public function testFindAllFetchesAndMapsAllDbRowsMatchingFilters()
    {
        $data = [
            ['title' => 'foo', 'pagecount' => '23'],
            ['title' => 'bar']
        ];
        $id = $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        // Execute
        $results = $this->bookRepository->findAll(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            }
        );
        // Assert
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Book::class, $results[0]);
        $this->assertEquals($this->expectedDefaultColumns, array_keys($results[0]->jsonSerialize()));
        $this->assertSame($id, $results[0]->getId());
        $this->assertSame($data[0]['title'], $results[0]->getTitle());
        $this->assertSame((int) $data[0]['pagecount'], $results[0]->getPagecount());
    }

    public function testFindAllWithColumnListMapsOnlySelectedColumns()
    {
        $data = [
            ['title' => 'foo', 'pagecount' => '23'],
            ['title' => 'bar', 'pagecount' => '24']
        ];
        $id = $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        $colsToSelect = ['title'];
        // Execute
        $results = $this->bookRepository->findAll(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            },
            $colsToSelect
        );
        // Assert
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Book::class, $results[0]);
        $this->assertEquals($colsToSelect, array_keys($results[0]->jsonSerialize()));
        $this->assertSame($data[0]['title'], $results[0]->getTitle());
        $this->assertSame(null, $results[0]->getPagecount());
    }

    public function testFindOneFetchesAndMapsSingleDbRowsMatchingFilters()
    {
        $data = [
            ['title' => 'foo', 'pagecount' => '23'],
            ['title' => 'bar']
        ];
        $id = $this->insertTestData($data[0]);
        $this->insertTestData($data[1]);
        // Execute
        $result = $this->bookRepository->findOne(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            }
        );
        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($this->expectedDefaultColumns, array_keys($result->jsonSerialize()));
        $this->assertSame($id, $result->getId());
        $this->assertSame($data[0]['title'], $result->getTitle());
        $this->assertSame((int) $data[0]['pagecount'], $result->getPagecount());
    }

    public function testFetchOneWithColumnListMapsOnlySelectedColumns()
    {
        $data = ['title' => 'foo', 'pagecount' => '23'];
        $colsToSelect = ['id', 'title'];
        $id = $this->insertTestData($data);
        // Execute
        $result = $this->bookRepository->findOne(
            function (QueryInterface $q) use ($id) {
                $q->where('id = :idv');
                $q->bindValue('idv', $id);
            },
            $colsToSelect
        );
        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($colsToSelect, array_keys($result->jsonSerialize()));
        $this->assertSame($id, $result->getId());
        $this->assertSame($data['title'], $result->getTitle());
        $this->assertSame(null, $result->getPagecount());
    }
}
