<?php

namespace Rad\Db;

use Closure;
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
     * @param Closure $filterApplier
     * @return JsonSerializable[]
     */
    public function findAll(Closure $filterApplier): array;

    /**
     * @param Closure $filterApplier
     * @return JsonSerializable
     */
    public function findOne(Closure $filterApplier): JsonSerializable;

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
