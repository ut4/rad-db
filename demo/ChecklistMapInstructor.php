<?php

namespace Demo;

use Rad\Db\Mappable;

class ChecklistMapInstructor implements Mappable
{
    public function getTableName(): string
    {
        return 'checklistItem';
    }

    public function getEntityClassPath(): string
    {
        return ChecklistItem::class;
    }

    public function getBindHints(): array
    {
        return [];
    }

    public function getIdColumnName(): string
    {
        return 'id';
    }
}
