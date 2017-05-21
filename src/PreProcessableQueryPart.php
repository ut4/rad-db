<?php

namespace Rad\Db;

interface PreProcessableQueryPart
{
    public function preProcess(QueryPart $mainQueryPart);
}
