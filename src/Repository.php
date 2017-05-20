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
     * @return JsonSerializable[]
     */
    public function selectAll(): array;

    /**
     * @param Callable $filterApplier TODO change to Aura\SqlQuery\Common\WhereInterface
     * @return JsonSerializable[]
     */
    public function findAll(Callable $filterApplier): array;

    /**
     * @param Callable $filterApplier TODO change to Aura\SqlQuery\Common\WhereInterface
     * @return JsonSerializable
     */
    public function findOne(Callable $filterApplier): JsonSerializable;

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
