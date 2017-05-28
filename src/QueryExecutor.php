<?php

namespace Rad\Db;

interface QueryExecutor
{
    public function exec(QueryPlanPart $queryPlanPart, QueryBuildingDb $db): int;
}
