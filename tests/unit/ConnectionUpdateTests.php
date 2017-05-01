<?php

namespace Rad\Db\Unit;

use PDOStatement;
use Aura\SqlQuery\Common\Update;

trait ConnectionUpdateTests
{
    public function testUpdateRunsPreparedStatementAndReturnsAffectedRowCount()
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
        $updateQ = $this->createMock(Update::class);
        $updateQ->method('getStatement')->willReturn($expectedQuery);
        $updateQ->method('getBindValues')->willReturn($expectedPrepareBindValues);
        $result = $this->connection->update($updateQ);
        // Assert
        $this->assertEquals(
            $mockRowCount,
            $result,
            'Should return the row count'
        );
    }
}
