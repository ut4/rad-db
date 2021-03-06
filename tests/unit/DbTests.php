<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\Connection;
use Rad\Db\Db;
use Aura\SqlQuery\Common\InsertInterface;
use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\UpdateInterface;
use Aura\SqlQuery\Common\DeleteInterface;

class DbTests extends TestCase
{
    private $mockConn;
    private $db;

    /**
     * @before
     */
    public function beforeEach()
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
            $mockValueFromConnection,
            $result,
            'Should return value directly from <connection>'
        );
    }

    public function testSelectAllCallsConnection()
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
            $mockValueFromConnection,
            $result,
            'Should return value directly from <connection>'
        );
    }

    public function testSelectAllCallsConnectionWithProvidedFetchArgs()
    {
        $selectAllQuery = $this->createMock(SelectInterface::class);
        $fetchArgs = ['PDO::FETCH_FOO'];
        $mockValueFromConnection = [1, 2];
        $this->mockConn->expects($this->once())
            ->method('fetchAll')
            ->with($selectAllQuery, $fetchArgs)
            ->willReturn($mockValueFromConnection);
        // Execute
        $this->db->selectAll($selectAllQuery, $fetchArgs);
    }

    public function testSelectOneCallsConnection()
    {
        $selectOneQuery = $this->createMock(SelectInterface::class);
        $mockValueFromConnection = ['col' => 'val'];
        $this->mockConn->expects($this->once())
            ->method('fetch')
            ->with($selectOneQuery)
            ->willReturn($mockValueFromConnection);
        // Execute
        $result = $this->db->selectOne($selectOneQuery);
        // Assert
        $this->assertEquals(
            $mockValueFromConnection,
            $result,
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
            $mockValueFromConnection,
            $result,
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
            $mockValueFromConnection,
            $result,
            'Should return value directly from <connection>'
        );
    }
}
