<?php

namespace Rad\Db\Unit;

use Rad\Db\Resources\JsonObject;

trait BasicCrudRepositoryUpdateTests
{
    public function testUpdateMapsAndUpdatesSingleItem()
    {
        $input = ['foo' => 'bar'];
        $where = function () {};
        $expectedOmitList = [$this->bookRepository->getIdColumnName()];
        $bindHints = [];
        $mockMappedData = new JsonObject();
        $this->mockMapper
            ->expects($this->once())
            ->method('map')
            ->with($input, $expectedOmitList, $bindHints)
            ->willReturn($mockMappedData);
        $mockRowCountFromDb = 1;
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('update')
            ->with($this->bookRepository->getTableName(), $mockMappedData, $where)
            ->willReturn($mockRowCountFromDb);
        // Execute
        $result = $this->bookRepository->update($input, $where, $bindHints);
        // Assert
        $this->assertEquals(
            $mockRowCountFromDb,
            $result,
            'Should return the row count from <queryBuildingDb>'
        );
    }

    public function testUpdateMakesDefaultFilterApplier()
    {
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('update')
            ->with(
                $this->anything(), // Table name
                $this->anything(), // Omit list
                $this->callback(function ($actual) {
                    // Should be a Callaback, not null
                    return is_callable($actual);
                })
            );
        // Execute & Assert
        $this->bookRepository->update([]);
    }
}
