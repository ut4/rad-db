<?php

namespace Rad\Db\Resources;

use Aura\SqlQuery\QueryInterface;
use PHPUnit\Framework\TestCase;

class QueryMockBuilder
{
    public function __construct(QueryInterface $mock, TestCase $phpunit)
    {
        $this->mock = $mock;
        $this->phpunit = $phpunit;
    }

    public function expect(string $method/*, $val1, $val2 ...*/)
    {
        return $this->mock->expects($this->phpunit->once())
            ->method($method)
            ->with(... array_slice(func_get_args(), 1))
            ->willReturn($this->mock);
    }

    public function getMock()
    {
        return $this->mock;
    }
}
