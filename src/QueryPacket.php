<?php

namespace Rad\Db;

class QueryPacket
{
    private $data;
    private $mapInstructor;
    private $bindHint;
    private $result;

    public function __construct(array $data, Mappable $mapInstructor)
    {
        $this->setData($data);
        $this->mapInstructor = $mapInstructor;
    }

    function getData(): array
    {
        return $this->data;
    }
    function setData(array $data)
    {
        $this->data = $data;
    }

    public function getMapInstructor(): Mappable
    {
        return $this->mapInstructor;
    }

    function getBindHint(): BindHint
    {
        return $this->bindHint;
    }
    function setBindHint(BindHint $bindHint)
    {
        $this->bindHint = $bindHint;
    }

    function getResult(): int
    {
        return $this->result;
    }
    function setResult(int $result)
    {
        $this->result = $result;
    }
}