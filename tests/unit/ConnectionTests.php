<?php

namespace Rad\Db;

use PDO;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    use ConnectionInsertTests;
    use ConnectionSelectTests;
    use ConnectionUpdateTests;
    use ConnectionDeleteTests;

    private $mockPdo;
    private $connection;

    public function __construct()
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->connection = new Connection($this->mockPdo);
    }
}
