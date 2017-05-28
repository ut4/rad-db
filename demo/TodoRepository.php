<?php

namespace Demo;

use Rad\Db\AutoRepository;
use Aura\SqlQuery\Common\SelectInterface;
use Closure;

class TodoRepository extends AutoRepository
{
    public function getMapInstructorClassPath(): string
    {
        return TodoMapInstructor::class;
    }

    public function selectAll(
        Closure $queryBuilderSetupFn = null,
        Closure $whereApplier = null
    ): array {
        if (!$queryBuilderSetupFn) {
            $queryBuilderSetupFn = function (SelectInterface $select) {
                $select->from('todoItem AS t');
                $select->leftJoin('checklistItem AS c', 'c.todoItemId = t.id');
                $select->cols([
                    't.id as id',
                    't.description as description',
                    't.due as due',
                    'c.id as checklist.id',
                    'c.description as checklist.description',
                    'c.todoItemId as checklist.todoItemId'
                ]);
            };
        }
        return parent::selectAll($queryBuilderSetupFn, $whereApplier);
    }
}
