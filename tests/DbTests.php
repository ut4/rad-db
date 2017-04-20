<?php

namespace Rad\Db;

use PHPUnit\Framework\TestCase;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Common\DeleteInterface;

class DbTest extends TestCase
{
    private $mockConn;
    private $db;

    public function __construct()
    {
        $this->mockConn = $this->createMock(Connection::class);
        $this->db = new Db($this->mockConn);
    }

    public function testInsertCallsConnection()
    {
        $insertQuery = $this->createMock(InsertInterface::class);
        $mockValueFromConnection = 24;
        $this->mockConn->expects($this->once())
            ->method('insert')
            ->with($insertQuery)
            ->willReturn($mockValueFromConnection);
        // Execute
        $result = $this->db->insert($insertQuery);
        // Assert
        $this->assertEquals(
            $result,
            $mockValueFromConnection,
            'Should return value directly from <connection>'
        );
    }

    public function selectAllCallsConnection()
    {
        $selectAllQuery = $this->createMock(SelectInterface::class);
        $mockValueFromConnection = [1, 2];
        $this->mockConn->expects($this->once())
            ->method('fetchAll')
            ->with($selectAllQuery)
            ->willReturn($mockValueFromConnection);
        // Execute
        $result = $this->db->selectAll($selectAllQuery);
        // Assert
        $this->assertEquals(
            $result,
            $mockValueFromConnection,
            'Should return value directly from <connection>'
        );
    }

    public function selectOneCallsConnectionAndReturnsFirstItemFromResults()
    {
        $selectOneQuery = $this->createMock(SelectInterface::class);
        $mockValueFromConnection = [1, 2];
        $this->mockConn->expects($this->once())
            ->method('fetchAll')
            ->with($selectOneQuery)
            ->willReturn($mockValueFromConnection);
        // Execute
        $result = $this->db->selectAll($selectOneQuery);
        // Assert
        $this->assertEquals(
            $result,
            $mockValueFromConnection[0],
            'Should return first value from the array returned by <connection>'
        );
    }

    public function testUpdateCallsConnection()
    {
        $updateQuery = $this->createMock(UpdateInterface::class);
        $mockValueFromConnection = 25;
        $this->mockConn->expects($this->once())
            ->method('update')
            ->with($updateQuery)
            ->willReturn($mockValueFromConnection);
        // Execute
        $result = $this->db->update($updateQuery);
        // Assert
        $this->assertEquals(
            $result,
            $mockValueFromConnection,
            'Should return value directly from <connection>'
        );
    }

    public function testDeleteCallsConnection()
    {
        $deleteQuery = $this->createMock(DeleteInterface::class);
        $mockValueFromConnection = 25;
        $this->mockConn->expects($this->once())
            ->method('delete')
            ->with($deleteQuery)
            ->willReturn($mockValueFromConnection);
        // Execute
        $result = $this->db->delete($deleteQuery);
        // Assert
        $this->assertEquals(
            $result,
            $mockValueFromConnection,
            'Should return value directly from <connection>'
        );
    }
}
