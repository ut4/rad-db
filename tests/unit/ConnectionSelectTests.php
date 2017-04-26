<?php

namespace Rad\Db;

use PDO;
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
            $mockResultsFromPdo,
            $result,
            'Should return the results'
        );
    }

    public function testFetchAllUsesProvidedFetchArgs()
    {
        // Prepare
        $fetchArgs = [PDO::FETCH_CLASS, 'Foo'];
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement
            ->expects($this->once())
            ->method('fetchAll')
            ->with($fetchArgs[0], $fetchArgs[1])
            ->willReturn([]);
        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($mockPreparedStatement);
        // Execute & Assert
        $selectQ = $this->createMock(Select::class);
        $this->connection->fetchAll($selectQ, $fetchArgs);
    }

    public function testFetchAllReturnsEmptyArrayIfStatementFails()
    {
        // Prepare
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement->method('fetchAll')->willReturn(false);
        $this->mockPdo->method('prepare')->willReturn($mockPreparedStatement);
        // Execute & Assert
        $selectQ = $this->createMock(Select::class);
        $result = $this->connection->fetchAll($selectQ);
        $this->assertEquals(
            [],
            $result
        );
    }
}
