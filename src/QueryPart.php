<?php

namespace Rad\Db;

class QueryPart
{
    protected $data;
    protected $instructor;

    public function __construct(array $data, Mappable $instructor)
    {
        $this->data = $data;
        $this->instructor = $instructor;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMapInstructor(): Mappable
    {
        return $this->instructor;
    }
}
