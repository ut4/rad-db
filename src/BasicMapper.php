<?php

namespace Rad\Db;

use JsonSerializable;
use InvalidArgumentException;

class BasicMapper implements Mapper
{
    private $entityClassPath;

    /**
     * @param string $entityClassPath
     */
    public function __construct(string $entityClassPath)
    {
        $this->setEntityClassPath($entityClassPath);
    }

    /**
     * @param array $input
     * @param array $omit = null
     * @param array $bindHints = null
     * @return JsonSerializable
     */
    public function map(
        array $input,
        array $omit = null,
        array $bindHints = null
    ): JsonSerializable {
        $entity = $this->makeNewEntity();
        $entity->__omit = $omit;
        foreach ($input as $name => $value) {
            if ($omit && in_array($name, $omit)) {
                continue;
            }
            $setterMethodName = 'set' . ucfirst($name);
            if (method_exists($entity, $setterMethodName)) {
                $entity->$setterMethodName($value);
            }
        }
        return $entity;
    }

    /**
     * @param array[] $inputs
     * @param array $omit = null
     * @param array $bindHints = null
     * @return JsonSerializable[]
     */
    public function mapAll(
        array $inputs,
        array $omit = null,
        array $bindHints = null
    ): array {
        return array_map(
            function (array $input) use ($omit, $bindHints) {
                return $this->map($input, $omit, $bindHints);
            },
            !isset($inputs[0]) ? [$inputs] : $inputs
        );
    }

    /**
     * @param string $entityClassPath
     * @throws InvalidArgumentException
     */
    public function setEntityClassPath(string $entityClassPath)
    {
        if (!in_array('JsonSerializable', class_implements($entityClassPath))) {
            throw new InvalidArgumentException(
                $entityClassPath . ' should implement \\JsonSerializable'
            );
        }
        $this->entityClassPath = $entityClassPath;
    }

    /**
     * @return JsonSerializable
     */
    private function makeNewEntity(): JsonSerializable
    {
        return new $this->entityClassPath();
    }
}
