<?php

namespace Rad\Db\Unit;

trait BasicCrudRepositoryInsertTests
{
    public function testInsertMapsAndInsertsMultipleItems()
    {
        $input = ['foo' => ['bar' => 'baz', 'haz' => ['naz' => 'gas']]];
        $bindHints = ['foo.haz' => '<AnotherEntityClass>'];
        $mockMappedData = [['<JsonSerializable>']];
        $expectedOmitList = [$this->testRepository->getIdColumnName()];
        $this->mockMapper
            ->expects($this->once())
            ->method('mapAll')
            ->with($input, $expectedOmitList, $bindHints)
            ->willReturn($mockMappedData);
        $mockRowCountFromDb = 1;
        $this->mockQueryBuildingDb
            ->expects($this->once())
            ->method('insert')
            ->with($this->testRepository->getTableName(), $mockMappedData)
            ->willReturn($mockRowCountFromDb);
        // Execute
        $result = $this->testRepository->insert($input, $bindHints);
        // Assert
        $this->assertEquals(
            $mockRowCountFromDb,
            $result,
            'Should return the row count from <queryBuildingDb>'
        );
    }
}
