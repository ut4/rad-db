<?php

namespace Rad\Db;

use JsonSerializable;

interface Repository
{
    /**
     * @param array|array[] $data
     * @return int
     */
    public function insert(array $data): int;

    /**
     * @param array $cols = null
     * @return JsonSerializable[]
     */
    public function selectAll(array $cols = null): array;

    /**
     * @param Callable $filterApplier TODO change to Aura\SqlQuery\Common\WhereInterface
     * @param array $cols = null
     * @return JsonSerializable[]
     */
    public function findAll(
        Callable $filterApplier,
        array $cols = null
    ): array;

    /**
     * @param Callable $filterApplier TODO change to Aura\SqlQuery\Common\WhereInterface
     * @param array $cols = null
     * @return JsonSerializable
     */
    public function findOne(
        Callable $filterApplier,
        array $cols = null
    ): JsonSerializable;


    /**
     * @param array $input
     * @return int
     */
    public function update(array $input): int;

    /**
     * @param array $input
     * @return int
     */
    public function delete(array $input): int;
}
