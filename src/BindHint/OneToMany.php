<?php

namespace Rad\Db\BindHint;

use Rad\Db\BindHint;
use Rad\Db\QueryPacket;

class OneToMany implements BindHint
{
    protected $targetPropertyName;
    protected $mapInstructorClassPath;

    public function __construct(
        string $targetPropertyName,
        string $mapInstructorClassPath
    ) {
        $this->targetPropertyName = $targetPropertyName;
        $this->mapInstructorClassPath = $mapInstructorClassPath;
    }

    public function getTargetPropertyName(): string
    {
        return $this->targetPropertyName;
    }

    public function getMapInstructorClassPath(): string
    {
        return $this->mapInstructorClassPath;
    }

    /**
     * input [['foo'=>'bar']], output [['foo'=>'bar','parentQtableParentQIdColumnName'=>42]]
     */
    public function preProcess(array $data, QueryPacket $parentQuery): array
    {
        return $data + [
            $parentQuery->getMapInstructor()->getTableName() .
            ucfirst($parentQuery->getMapInstructor()->getIdColumnName()) =>
                    $parentQuery->getResult()
        ];
    }
}
