<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase;
use Rad\Db\Connection;
use PDO;

class ConnectionTest extends TestCase
{
    use ConnectionInsertTests;
    use ConnectionSelectTests;
    use ConnectionUpdateTests;
    use ConnectionDeleteTests;

    private $mockPdo;
    private $connection;

    /**
     * @before
     */
    public function beforeEach()
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->connection = new Connection($this->mockPdo);
    }
}
