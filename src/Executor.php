<?php

namespace Rad\Db;

interface Executor
{
    public function exec(QueryPart $queryPart, QueryBuildingDb $db): int;
}
