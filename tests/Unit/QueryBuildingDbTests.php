<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Aura\SqlQuery\QueryFactory;
use Rad\Db\Db;
use Rad\Db\QueryBuildingDb;
use Rad\Db\Resources\QueryMockBuilder;
use Aura\SqlQuery\Common\Insert;
use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\Common\Update;
use Aura\SqlQuery\Common\Delete;
use Rad\Db\Resources\JsonObject;

class QueryBuildingDbTests extends TestCase
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

    public function testInsertBuildsQueryAndCallsBaseDb()
    {
        $tableToInsertTo = 'atable';
        $dataToInsert = new JsonObject();
        $dataToInsert->foo = 'bar';
        $mockBuilder = new QueryMockBuilder($this->createMock(Insert::class), $this);
        $mockBuilder->expect('into', $tableToInsertTo);
        $mockBuilder->expect('cols', $dataToInsert->jsonSerialize());
        $mockInsertQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newInsert')
            ->willReturn($mockInsertQuery);
        $mockInsertIdFromBaseDb = 23;
        $this->mockBaseDb->expects($this->once())
            ->method('insert')
            ->with($mockInsertQuery)
            ->willReturn($mockInsertIdFromBaseDb);
        // Execute
        $result = $this->qbDb->insert($tableToInsertTo, $dataToInsert);
        $this->assertEquals(
            $mockInsertIdFromBaseDb,
            $result,
            'Should return the insertId from <db>'
        );
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInsertManyThrowsIfDataIsNotJsonSerializable()
    {
        $this->qbDb->insertMany('sometable', ['foo' => 'bar']);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInsertManyThrowsIfDataIsNotJsonSerializable2()
    {
        $this->qbDb->insertMany('sometable', [new \stdClass()]);
    }

    public function testInsertManyBuildsQueryAndCallsBaseDb()
    {
        $tableToInsertTo = 'qtwetable';
        $firstItem = new JsonObject();
        $firstItem->foo = 'bar';
        $secondItem = new JsonObject();
        $secondItem->foo = 'asz';
        $thirdItem = new JsonObject();
        $thirdItem->foo = 'hiz';
        $mockBuilder = new QueryMockBuilder($this->createMock(Insert::class), $this);
        $mockBuilder->expect('into', $tableToInsertTo);
        // https://github.com/auraphp/Aura.SqlQuery/blob/3.x/docs/insert.md
        $mockBuilder->expect('cols', $firstItem->jsonSerialize());
        $mockBuilder->expect('addRows', [
            $secondItem->jsonSerialize(),
            $thirdItem->jsonSerialize()
        ]);
        $mockInsertQuery = $mockBuilder->getMock();
        $this->mockQueryFactory->expects($this->once())
            ->method('newInsert')
            ->willReturn($mockInsertQuery);
        $mockInsertIdFromBaseDb = 23;
        $this->mockBaseDb->expects($this->once())
            ->method('insert')
            ->with($mockInsertQuery)
            ->willReturn($mockInsertIdFromBaseDb);
        // Execute
        $result = $this->qbDb->insertMany(
            $tableToInsertTo,
            [$firstItem, $secondItem, $thirdItem]
        );
        $this->assertEquals(
            $mockInsertIdFromBaseDb,
            $result,
            'Should return the insertId from <db>'
        );
    }

    public function testSelectAllBuildsQueryAndCallsBaseDb()
    {
        $this->assertSelectBuildsQueryAndCallsBaseDb('selectAll');
    }

    public function testSelectAllCallsFilterApplierCallback()
    {
        $this->assertSelectCallsFilterApplierCallback('selectAll');
    }

    public function testSelectOneBuildsQueryAndCallsBaseDb()
    {
        $this->assertSelectBuildsQueryAndCallsBaseDb('selectOne');
    }

    public function testSelectOneCallsFilterApplierCallback()
    {
        $this->assertSelectCallsFilterApplierCallback('selectOne');
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

    private function assertSelectBuildsQueryAndCallsBaseDb(
        string $selectMethod
    ) {
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
            ->method($selectMethod)
            ->with($mockSelectQuery)
            ->willReturn($mockResultsFromBaseDb);
        // Execute
        $result = $this->qbDb->$selectMethod(
            $tableToSelectFrom,
            $columnsToSelect
        );
        $this->assertEquals(
            $mockResultsFromBaseDb,
            $result,
            'Should return the results from <db>'
        );
    }

    private function assertSelectCallsFilterApplierCallback(
        string $selectMethod
    ) {
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
            ->method($selectMethod)
            ->with($mockSelectQuery)
            ->willReturn($mockResultsFromBaseDb);
        // Execute
        $result = $this->qbDb->$selectMethod(
            $tableToSelectFrom,
            $columnsToSelect,
            $filterApplier
        );
        $this->assertEquals(
            $mockResultsFromBaseDb,
            $result,
            'Should return the results from <db>'
        );
    }
}
