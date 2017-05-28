<?php

namespace Rad\Db;

interface CollectExecutor
{
    public function exec(FetchPlanPart $fpp, array $fetchResults, array &$mapped);
}
