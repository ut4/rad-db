<?php

namespace Rad\Db\Unit;

use PDO;
use PDOStatement;
use Aura\SqlQuery\Common\Select;

trait ConnectionSelectTests
{
    public function testFetchAllRunsPreparedStatementAndReturnsTheResults()
    {
        $this->assertSuccefulFetchReturnsTheResultsFromStatement('fetchAll');
    }

    public function testFetchAllUsesProvidedFetchArgs()
    {
        $this->assertFetchAreCalledWithFetchArgs(
            [PDO::FETCH_CLASS, 'Foo'],
            'fetchAll'
        );
    }

    public function testFetchAllReturnsEmptyArrayIfStatementFails()
    {
        $this->assertFailedFetchReturnsEmptyArray('fetchAll');
    }

    public function testFetchRunsPreparedStatementAndReturnsTheResult()
    {
        $this->assertSuccefulFetchReturnsTheResultsFromStatement('fetch');
    }

    public function testFetchUsesProvidedFetchArgs()
    {

        $this->assertFetchAreCalledWithFetchArgs(
            [PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT],
            'fetch'
        );
    }

    public function testFetchReturnsEmptyArrayIfStatementFails()
    {
        $this->assertFailedFetchReturnsEmptyArray('fetch');
    }

    private function assertSuccefulFetchReturnsTheResultsFromStatement(
        string $pdoFetchMethod
    ) {
        // Prepare
        $expectedPrepareQuery = '<sql>';
        $expectedPrepareBindValues = '<data>';
        $mockResultsFromPdo = [];
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement
            ->expects($this->once())
            ->method($pdoFetchMethod)
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
        $result = $this->connection->$pdoFetchMethod($selectQ);
        // Assert
        $this->assertEquals(
            $mockResultsFromPdo,
            $result,
            'Should return the results'
        );
    }

    private function assertFetchAreCalledWithFetchArgs(
        array $fetchArgs,
        string $pdoFetchMethod
    ) {
        // Prepare
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement
            ->expects($this->once())
            ->method($pdoFetchMethod)
            ->with(... $fetchArgs)
            ->willReturn([]);
        $this->mockPdo
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($mockPreparedStatement);
        // Execute & Assert
        $selectQ = $this->createMock(Select::class);
        $this->connection->$pdoFetchMethod($selectQ, $fetchArgs);
    }

    private function assertFailedFetchReturnsEmptyArray(string $pdoFetchMethod)
    {
        // Prepare
        $mockPreparedStatement = $this->createMock(PDOStatement::class);
        $mockPreparedStatement->method($pdoFetchMethod)->willReturn(false);
        $this->mockPdo->method('prepare')->willReturn($mockPreparedStatement);
        // Execute & Assert
        $selectQ = $this->createMock(Select::class);
        $result = $this->connection->$pdoFetchMethod($selectQ);
        $this->assertEquals([], $result);
    }
}
