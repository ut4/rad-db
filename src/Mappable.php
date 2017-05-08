<?php

namespace Rad\Db;

interface Mappable
{
    /**
     * @return string
     */
    public function getEntityClassPath(): string;

    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @return string
     */
    public function getIdColumnName(): string;

    /**
     * @return BindHint[]|array
     */
    public function getBindHints(): array;
}
