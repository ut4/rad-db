<?php

namespace Rad\Db;

use InvalidArgumentException;

class BindHint
{
    const TYPES = ['HasMany' => 'HasMany'];

    private $targetPropertyName;
    private $bindType;
    private $instructorClassPath;
    private $originIdCol;

    public function __construct(
        string $bindType,
        string $targetPropertyName,
        string $instructorClassPath
    ) {
        if (!array_key_exists($bindType, static::TYPES)) {
            throw new InvalidArgumentException(sprintf(
                'Bint type %s not implemented. Available types: %s',
                $bindType,
                \implode(', ', \array_keys(static::TYPES))
            ));
        }
        $this->bindType = static::TYPES[$bindType];
        $this->targetPropertyName = $targetPropertyName;
        $this->instructorClassPath = $instructorClassPath;
    }

    /**
     * @return string
     */
    public function getBindType(): string
    {
        return $this->bindType;
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

    /**
     * @return string
     */
    public function getOriginIdCol(): string
    {
        return $this->originIdCol;
    }

    public function setOriginIdCol(string $originIdCol)
    {
        $this->originIdCol = $originIdCol;
    }
}
