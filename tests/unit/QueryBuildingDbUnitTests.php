<?php

namespace Rad\Db;

use PHPUnit\Framework\TestCase;
use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\Common\Insert;
use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\Common\Update;
use Aura\SqlQuery\Common\Delete;
use Rad\Db\Resources\QueryMockBuilder;

class QueryBuildingDbUnitTests extends TestCase
{
    private $mockQueryFactory;
    private $mockBaseDb;
    private $qbDb;

    public function __construct()
    {
        $this->mockQueryFactory = $this->createMock(QueryFactory::class);
        $this->mockBaseDb = $this->createMock(Db::class);
        $this->qbDb = new QueryBuildingDb(
            $this->mockBaseDb,
            $this->mockQueryFactory
        );
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInsertThrowsIfDataIsNotJsonSerializable()
    {
        $this->qbDb->insert('sometable', [[]]);
    }

    public function testInsertBuildsQueryAndCallsBaseDb()
    {
        $tableToInsertTo = 'rty';
        $dataToInsert = new JsonObject();
        $dataToInsert->foo = 'bar';
        $mockBuilder = new QueryMockBuilder($this->createMock(Insert::class), $this);
        $mockBuilder->expect('into', $tableToInsertTo);
        $mockBuilder->expect('cols', $dataToInsert->jsonSerialize());
        $mockInsertQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newInsert')
            ->willReturn($mockInsertQuery);
        $mockRowCountFromBaseDb = 23;
        $this->mockBaseDb->expects($this->once())
            ->method('insert')
            ->with($mockInsertQuery)
            ->willReturn($mockRowCountFromBaseDb);
        // Execute
        $result = $this->qbDb->insert($tableToInsertTo, [$dataToInsert]);
        $this->assertEquals(
            $mockRowCountFromBaseDb,
            $result,
            'Should return the row count from <db>'
        );
    }

    public function testSelectAllBuildsQueryAndCallsBaseDb()
    {
        $tableToSelectFrom = 'ser';
        $columnsToSelect = ['foo'];
        $mockBuilder = new QueryMockBuilder($this->createMock(Select::class), $this);
        $mockBuilder->expect('from', $tableToSelectFrom);
        $mockBuilder->expect('cols', $columnsToSelect);
        $mockSelectQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newSelect')
            ->willReturn($mockSelectQuery);
        $mockResultsFromBaseDb = [['foo' => 'bar']];
        $this->mockBaseDb->expects($this->once())
            ->method('selectAll')
            ->with($mockSelectQuery)
            ->willReturn($mockResultsFromBaseDb);
        // Execute
        $result = $this->qbDb->selectAll(
            $tableToSelectFrom,
            $columnsToSelect
        );
        $this->assertEquals(
            $mockResultsFromBaseDb,
            $result,
            'Should return the results from <db>'
        );
    }

    public function testSelectAllCallsFilterApplierCallback()
    {
        $tableToSelectFrom = 'tyue';
        $columnsToSelect = ['rtt', 'ty'];
        $where = ['col' => 'id', 'value' => 12];
        $filterApplier = function ($q) use ($where) {
            $q->where($where['col'], $where['value']);
        };
        $mockBuilder = new QueryMockBuilder($this->createMock(Select::class), $this);
        $mockBuilder->expect('from', $tableToSelectFrom);
        $mockBuilder->expect('cols', $columnsToSelect);
        $mockBuilder->expect('where', $where['col'], $where['value']);
        $mockSelectQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newSelect')
            ->willReturn($mockSelectQuery);
        $mockResultsFromBaseDb = [['foo' => 'bar']];
        $this->mockBaseDb->expects($this->once())
            ->method('selectAll')
            ->with($mockSelectQuery)
            ->willReturn($mockResultsFromBaseDb);
        // Execute
        $result = $this->qbDb->selectAll(
            $tableToSelectFrom,
            $columnsToSelect,
            null, // fetchArgs
            $filterApplier
        );
        $this->assertEquals(
            $mockResultsFromBaseDb,
            $result,
            'Should return the results from <db>'
        );
    }

    public function testUpdateBuildsQueryAndCallsBaseDb()
    {
        $tableToModify = 'qwe';
        $dataToUpdate = new JsonObject();
        $dataToUpdate->baz = 'haz';
        $mockBuilder = new QueryMockBuilder($this->createMock(Update::class), $this);
        $mockBuilder->expect('table', $tableToModify);
        $mockBuilder->expect('cols', ['baz']);
        $mockBuilder->expect('bindValues', $dataToUpdate->jsonSerialize());
        $mockUpdateQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newUpdate')
            ->willReturn($mockUpdateQuery);
        $mockRowCountFromBaseDb = 98;
        $this->mockBaseDb->expects($this->once())
            ->method('update')
            ->with($mockUpdateQuery)
            ->willReturn($mockRowCountFromBaseDb);
        // Execute
        $result = $this->qbDb->update(
            $tableToModify,
            $dataToUpdate
        );
        $this->assertEquals(
            $mockRowCountFromBaseDb,
            $result,
            'Should return the row count from <db>'
        );
    }

    public function testUpdateCallsFilterApplierCallback()
    {
        $tableToModify = 'ryye';
        $dataToUpdate = new JsonObject();
        $dataToUpdate->baz = 'haz';
        $where = ['col' => 'id', 'value' => 12];
        $filterApplier = function ($q) use ($where) {
            $q->where($where['col'], $where['value']);
        };
        $mockBuilder = new QueryMockBuilder($this->createMock(Update::class), $this);
        $mockBuilder->expect('table', $tableToModify);
        $mockBuilder->expect('cols', ['baz']);
        $mockBuilder->expect('bindValues', $dataToUpdate->jsonSerialize());
        $mockBuilder->expect('where', $where['col'], $where['value']);
        $mockUpdateQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newUpdate')
            ->willReturn($mockUpdateQuery);
        $mockRowCountFromBaseDb = 123;
        $this->mockBaseDb->expects($this->once())
            ->method('update')
            ->with($mockUpdateQuery)
            ->willReturn($mockRowCountFromBaseDb);
        // Execute
        $result = $this->qbDb->update(
            $tableToModify,
            $dataToUpdate,
            $filterApplier
        );
        $this->assertEquals(
            $mockRowCountFromBaseDb,
            $result,
            'Should return the row count from <db>'
        );
    }

    public function testDeleteBuildsQueryAndCallsBaseDb()
    {
        $tableToDeleteFrom = 'weret';
        $where = ['col' => 'id', 'value' => 12];
        $filterApplier = function ($q) use ($where) {
            $q->where($where['col'], $where['value']);
        };
        $mockBuilder = new QueryMockBuilder($this->createMock(Delete::class), $this);
        $mockBuilder->expect('from', $tableToDeleteFrom);
        $mockBuilder->expect('where', $where['col'], $where['value']);
        $mockDeleteQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newDelete')
            ->willReturn($mockDeleteQuery);
        $mockRowCountFromBaseDb = 345;
        $this->mockBaseDb->expects($this->once())
            ->method('delete')
            ->with($mockDeleteQuery)
            ->willReturn($mockRowCountFromBaseDb);
        // Execute
        $result = $this->qbDb->delete($tableToDeleteFrom, $filterApplier);
        $this->assertEquals(
            $mockRowCountFromBaseDb,
            $result,
            'Should return the row count from <db>'
        );
    }
}
