<?php

namespace Rad\Db\Unit;

use PDOStatement;
use Aura\SqlQuery\Common\Delete;

trait ConnectionDeleteTests
{
    public function testDeleteRunsPreparedStatementAndReturnsAffectedRowCount()
    {
        $expectedQuery = '<sql>';
        $expectedPrepareBindValues = '<data>';
        $mockRowCount = 1;
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn($mockRowCount);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('execute')
            ->with($expectedPrepareBindValues)
            ->willReturn($mockPreparedStatement);
        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->with($expectedQuery)
            ->willReturn($mockPreparedStatement);
        // Execute
        $deleteQ = $this->createMock(Delete::class);
        $deleteQ->method('getStatement')->willReturn($expectedQuery);
        $deleteQ->method('getBindValues')->willReturn($expectedPrepareBindValues);
        $result = $this->connection->delete($deleteQ);
        // Assert
        $this->assertEquals(
            $mockRowCount,
            $result,
            'Should return the row count'
        );
    }
}
