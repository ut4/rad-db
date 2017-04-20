<?php

namespace Rad\Db;

use PDO;
use PHPUnit\Framework\TestCase;
include_once __dir__ . '/ConnectionInsertTests.php';
include_once __dir__ . '/ConnectionSelectTests.php';
include_once __dir__ . '/ConnectionUpdateTests.php';

class ConnectionTest extends TestCase
{
    use ConnectionInsertTests;
    use ConnectionSelectTests;
    use ConnectionUpdateTests;

    private $mockPdo;
    private $connection;

    public function __construct()
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->connection = new Connection($this->mockPdo);
    }
}
