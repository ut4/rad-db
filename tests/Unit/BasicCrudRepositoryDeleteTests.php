<?php

namespace Rad\Db\Unit;

trait BasicCrudRepositoryDeleteTests
{
    public function testDeleteCallsQueryBuildingDb()
    {
        $where = function () {};
        $mockRowCountFromDb = 1;
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('delete')
            ->with($this->bookMappingInstructor->getTableName(), $where)
            ->willReturn($mockRowCountFromDb);
        // Execute
        $result = $this->bookRepository->delete([], $where);
        // Assert
        $this->assertEquals(
            $mockRowCountFromDb,
            $result,
            'Should return the row count from <queryBuildingDb>'
        );
    }

    public function testDeleteMakesDefaultFilterApplier()
    {
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->anything(), // Table name
                $this->callback(function ($actual) {
                    // Should be a Callaback, not null
                    return is_callable($actual);
                })
            );
        // Execute & Assert
        $this->bookRepository->delete(['id' => 2]);
    }
}
