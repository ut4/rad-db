<?php

namespace Demo;

use Rad\Db\Mappable;
use Rad\Db\BindHint;

class TodoMapInstructor implements Mappable
{
    public function getTableName(): string
    {
        return 'todoItem';
    }

    public function getEntityClassPath(): string
    {
        return TodoItem::class;
    }

    public function getBindHints(): array
    {
        return [new BindHint(BindHint::TYPES['HasMany'], 'checklist', ChecklistMapInstructor::class)];
    }

    public function getIdColumnName(): string
    {
        return 'id';
    }
}
