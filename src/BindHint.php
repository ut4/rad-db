<?php

namespace Rad\Db;

class BindHint
{
    private $targetPropertyName;
    private $queryPartClassPath;
    private $instructorClassPath;

    public function __construct(
        string $targetPropertyName,
        string $queryPartClassPath,
        string $instructorClassPath
    ) {
        $this->targetPropertyName = $targetPropertyName;
        $this->queryPartClassPath = $queryPartClassPath;
        $this->instructorClassPath = $instructorClassPath;
    }

    /**
     * @return string
     */
    public function getQueryPartClassPath(): string
    {
        return $this->queryPartClassPath;
    }

    /**
     * @return string
     */
    public function getTargetPropertyName(): string
    {
        return $this->targetPropertyName;
    }

    /**
     * @return string
     */
    public function getMapInstructorClassPath(): string
    {
        return $this->instructorClassPath;
    }
}
