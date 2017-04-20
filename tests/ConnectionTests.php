<?php

namespace Rad\Db;

use PDO;
use PHPUnit\Framework\TestCase;
include_once __dir__ . '/ConnectionInsertTests.php';

class ConnectionTest extends TestCase
{
    use ConnectionInsertTests;

    private $mockPdo;
    private $connection;

    public function __construct()
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->connection = new Connection($this->mockPdo);
    }
}
