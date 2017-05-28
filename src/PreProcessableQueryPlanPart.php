<?php

namespace Rad\Db;

interface PreProcessableQueryPlanPart
{
    public function preProcess(QueryPlanPart $mainQueryPlanPart);
}
