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
     * @param string $entityClassPath = null
     * @return JsonSerializable
     */
    public function map(
        array $input,
        array $omit = null,
        string $entityClassPath = null
    ): JsonSerializable {
        $entity = $this->makeNewEntity($entityClassPath);
        $entity->omitThese($omit);
        foreach ($input as $name => $value) {
            // Value explicitly marked as "ignore me pls" -> do nothing
            if ($omit && in_array($name, $omit)) {
                continue;
            }
            // Value has no setter -> do nothing
            $setterMethodName = 'set' . ucfirst($name);
            if (!method_exists($entity, $setterMethodName)) {
                continue;
            }
            // Setter found -> call it and mark $name as set
            $entity->$setterMethodName($value);
            $entity->markIsSet($name);
        }
        return $entity;
    }

    /**
     * @param array[] $inputs
     * @param array $omit = null
     * @param string $entityClassPath = null
     * @return JsonSerializable[]
     */
    public function mapAll(
        array $inputs,
        array $omit = null,
        string $entityClassPath = null
    ): array {
        if (!$inputs) {
            return [];
        }
        return array_map(
            function (array $input) use ($omit, $entityClassPath) {
                return $this->map($input, $omit, $entityClassPath);
            },
            !isset($inputs[0]) ? [$inputs] : $inputs
        );
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];
        foreach (get_class_methods($this->entityClassPath) as $methodName) {
            if (substr($methodName, 0, 3) !== 'set') {
                continue;
            }
            // setFoo -> Foo -> foo, setSomeProp -> SomeProp -> someProp
            $keys[] = lcfirst(substr($methodName, 3));
        }
        return $keys;
    }

    /**
     * @param string $entityClassPath
     * @throws InvalidArgumentException
     */
    public function setEntityClassPath(string $entityClassPath)
    {
        if (!is_subclass_of($entityClassPath, JsonObject::class)) {
            throw new InvalidArgumentException(
                $entityClassPath . ' should extend \\Rad\\Db\\JsonObject'
            );
        }
        $this->entityClassPath = $entityClassPath;
    }

    /**
     * @param string $entityClassPath = null
     * @return JsonObject
     */
    private function makeNewEntity(string $entityClassPath = null): JsonObject
    {
        return !$entityClassPath ? new $this->entityClassPath() : new $entityClassPath();
    }
}
