<?php

namespace Rad\Db;

use PDOStatement;
use Aura\SqlQuery\Common\Select;

trait ConnectionSelectTests
{
    public function testFetchAllRunsPreparedStatementAndReturnsTheResults()
    {
        // Prepare
        $expectedPrepareQuery = '<sql>';
        $expectedPrepareBindValues = '<data>';
        $mockResultsFromPdo = [];
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockResultsFromPdo);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('execute')
            ->with($expectedPrepareBindValues)
            ->willReturn($mockPreparedStatement);
        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->with($expectedPrepareQuery)
            ->willReturn($mockPreparedStatement);
        // Execute
        $selectQ = $this->createMock(Select::class);
        $selectQ->method('getStatement')->willReturn($expectedPrepareQuery);
        $selectQ->method('getBindValues')->willReturn($expectedPrepareBindValues);
        $result = $this->connection->fetchAll($selectQ);
        // Assert
        $this->assertEquals(
            $result,
            $mockResultsFromPdo,
            'Should return the results'
        );
    }
}
