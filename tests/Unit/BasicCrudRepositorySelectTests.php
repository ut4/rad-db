<?php

namespace Rad\Db\Unit;

use Rad\Db\Resources\JsonObject;

trait BasicCrudRepositorySelectTests
{
    public function testSelectAllFetchesAndMapsMultipleRows()
    {
        $columns = ['foo', 'bar AS nar'];
        $mockRowsFromDb = [['foo' => 'bar', 'nar' => 'qar']];
        $mockMappedData = [new JsonObject()];
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('selectAll')
            ->with($this->bookMapInstructor->getTableName(), $columns)
            ->willReturn($mockRowsFromDb);
        $this->mockMapper
            ->expects($this->once())
            ->method('mapAll')
            ->with($mockRowsFromDb)
            ->willReturn($mockMappedData);
        // Execute
        $result = $this->bookRepository->selectAll($columns);
        // Assert
        $this->assertEquals(
            $mockMappedData,
            $result,
            'Should return the mapped <queryBuildingDb> rows'
        );
    }

    public function testSelectAllUsesDefaultColumns()
    {
        $someKeys = ['foo', 'bar'];
        $this->mockMapper
            ->expects($this->once())
            ->method('getKeys')
            ->willReturn($someKeys);
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('selectAll')
            ->with($this->bookMapInstructor->getTableName(), $someKeys);
        // Execute & Assert
        $this->bookRepository->selectAll();
    }

    public function testFindAllFetchesAndMapsMultipleRows()
    {
        $columns = ['foo', 'bar AS nar'];
        $where = function () {};
        $mockRowsFromDb = [['foo' => 'bar', 'nar' => 'qar']];
        $mockMappedData = [new JsonObject()];
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('selectAll')
            ->with($this->bookMapInstructor->getTableName(), $columns, $where)
            ->willReturn($mockRowsFromDb);
        $this->mockMapper
            ->expects($this->once())
            ->method('mapAll')
            ->with($mockRowsFromDb)
            ->willReturn($mockMappedData);
        // Execute
        $result = $this->bookRepository->findAll($where, $columns);
        // Assert
        $this->assertEquals(
            $mockMappedData,
            $result,
            'Should return the mapped <queryBuildingDb> rows'
        );
    }

    public function testFindAllUsesDefaultColumns()
    {
        $someKeys = ['nar', 'gar'];
        $this->mockMapper
            ->expects($this->once())
            ->method('getKeys')
            ->willReturn($someKeys);
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('selectAll')
            ->with($this->bookMapInstructor->getTableName(), $someKeys);
        // Execute & Assert
        $where = function () {};
        $this->bookRepository->findAll($where);
    }

    public function testFindOneFetchesAndMapsASingleRow()
    {
        $columns = ['foo', 'bar AS nar'];
        $where = function () {};
        $mockRowFromDb = ['foo' => 'bar', 'nar' => 'qar'];
        $mockMappedData = new JsonObject();
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('selectOne')
            ->with($this->bookMapInstructor->getTableName(), $columns, $where)
            ->willReturn($mockRowFromDb);
        $this->mockMapper
            ->expects($this->once())
            ->method('map')
            ->with($mockRowFromDb)
            ->willReturn($mockMappedData);
        // Execute
        $result = $this->bookRepository->findOne($where, $columns);
        // Assert
        $this->assertEquals(
            $mockMappedData,
            $result,
            'Should return the mapped <queryBuildingDb> row'
        );
    }

    public function testFindOneUsesDefaultColumns()
    {
        $someKeys = ['bar', 'var'];
        $this->mockMapper
            ->expects($this->once())
            ->method('getKeys')
            ->willReturn($someKeys);
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('selectOne')
            ->with($this->bookMapInstructor->getTableName(), $someKeys);
        // Execute & Assert
        $where = function () {};
        $this->bookRepository->findOne($where);
    }
}
