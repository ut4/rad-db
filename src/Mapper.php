<?php

namespace Rad\Db;

use JsonSerializable;

interface Mapper
{
    /**
     * @param array $input
     * @return JsonSerializable
     */
    public function map(array $input, array $omit = []): JsonSerializable;
    /**
     * @param array[] $inputs
     * @return JsonSerializable[]
     */
    public function mapAll(array $inputs, array $omit = []): array;
}
