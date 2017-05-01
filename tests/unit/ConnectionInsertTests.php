<?php

namespace Rad\Db\Unit;

use PDOStatement;
use Aura\SqlQuery\Common\Insert;

trait ConnectionInsertTests
{
    public function testInsertRunsPreparedStatementAndReturnsLastInsertIdAsInt()
    {
        $expectedQuery = '<sql>';
        $expectedPrepareBindValues = '<data>';
        $mockLastInsertId = '2';
        $mockRowCount = 1;
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        // Should return <lastInsertId> if ...
        $this->mockPdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn($mockLastInsertId);
        // the return value of <preparedStatement>->rowCount() is greater than 0
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
        $insertQ = $this->createMock(Insert::class);
        $insertQ->method('getStatement')->willReturn($expectedQuery);
        $insertQ->method('getBindValues')->willReturn($expectedPrepareBindValues);
        $result = $this->connection->insert($insertQ);
        // Assert
        $this->assertEquals(
            (int) $mockLastInsertId,
            $result,
            'Should return the lastInsertId as an integer'
        );
    }

    public function testInsertReturnsZeroIfRowCountIsLessThanOne()
    {
        // Prepare
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('execute')
            ->willReturn($mockPreparedStatement);
        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($mockPreparedStatement);
        // Execute
        $insertQ = $this->createMock(Insert::class);
        $insertQ->method('getStatement')->willReturn('<sql>');
        $insertQ->method('getBindValues')->willReturn('<data>');
        $result = $this->connection->insert($insertQ);
        // Assert
        $this->assertEquals(0, $result, 'Should return 0');
    }
}
