<?php

namespace Rad\Db;

use JsonSerializable;

class Mapper
{
    private $entityClassPath;

    /**
     * @param string $entityClassPath = null
     */
    public function __construct(string $entityClassPath = null)
    {
        $entityClassPath && $this->setEntityClassPath($entityClassPath);
    }

    /**
     * @param array $input
     * @param string $entityClassPath = null
     * @param array $omit = null
     * @return JsonSerializable
     */
    public function map(
        array $input,
        string $entityClassPath = null,
        array $omit = null
    ): JsonSerializable {
        $entity = $this->makeNewEntity($entityClassPath);
        $entity->omitThese($omit);
        foreach ($input as $name => $value) {
            // Value explicitly marked as "ignore me pls" -> do nothing
            if ($omit && in_array($name, $omit)) {
                continue;
            }
            $setterMethodName = 'set' . ucfirst($name);
            // Value has no setter -> do nothing
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
     * @param string $entityClassPath = null
     * @param array $omit = null
     * @return JsonSerializable[]
     */
    public function mapAll(
        array $inputs,
        string $entityClassPath = null,
        array $omit = null
    ): array {
        if (!$inputs) {
            return [];
        }
        return array_map(
            function (array $input) use ($entityClassPath, $omit) {
                return $this->map($input, $entityClassPath, $omit);
            },
            isset($inputs[0]) ? $inputs : [$inputs]
        );
    }

    /**
     * @return array
     */
    public function getKeys(string $entityClassPath = null): array
    {
        $keys = [];
        $methods = get_class_methods( $entityClassPath ?? $this->entityClassPath);
        foreach ($methods as $methodName) {
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
