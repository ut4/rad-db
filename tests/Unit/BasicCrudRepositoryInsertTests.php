<?php

namespace Rad\Db\Unit;

use Rad\Db\Resources\JsonObject;

trait BasicCrudRepositoryInsertTests
{
    public function testInsertMapsAndInsertsSingleItem()
    {
        $input = ['foo' => 'bar'];
        $mockMappedItem = new JsonObject();
        $expectedOmitList = [$this->bookMappingInstructor->getIdColumnName()];
        $this->mockMapper
            ->expects($this->once())
            ->method('map')
            ->with($input, $expectedOmitList)
            ->willReturn($mockMappedItem);
        $mockInsertIdFromDb = 1;
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('insert')
            ->with($this->bookMappingInstructor->getTableName(), $mockMappedItem)
            ->willReturn($mockInsertIdFromDb);
        // Execute
        $result = $this->bookRepository->insert($input);
        // Assert
        $this->assertEquals(
            $mockInsertIdFromDb,
            $result,
            'Should return the lastInsertId from <queryBuildingDb>'
        );
    }

    public function testInsertMapsAndInsertsMultipleItems()
    {
        $inputs = [
            ['foo' => ['bar' => 'baz', 'haz' => ['naz' => 'gas']]],
            ['baz' => 'naz']
        ];
        $bindHints = ['foo.haz' => '<AnotherEntityClass>'];
        $mockMappedItems = [new JsonObject(), new JsonObject()];
        $expectedOmitList = [$this->bookMappingInstructor->getIdColumnName()];
        $this->mockMapper
            ->expects($this->once())
            ->method('mapAll')
            ->with($inputs, $expectedOmitList)
            ->willReturn($mockMappedItems);
        $mockInsertIdFromDb = 2;
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('insertMany')
            ->with($this->bookMappingInstructor->getTableName(), $mockMappedItems)
            ->willReturn($mockInsertIdFromDb);
        // Execute
        $result = $this->bookRepository->insert($inputs, $bindHints);
        // Assert
        $this->assertEquals(
            $mockInsertIdFromDb,
            $result,
            'Should return the lastInsertId from <queryBuildingDb>'
        );
    }
}
