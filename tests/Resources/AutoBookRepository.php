<?php

namespace Rad\Db\Resources;

use Rad\Db\AutoRepository;
use Rad\Db\Resources\BookMappings;
use Aura\SqlQuery\Common\SelectInterface;
use Closure;

class AutoBookRepository extends AutoRepository
{
    /**
     * @return string
     */
    public function getMapInstructorClassPath(): string
    {
        return BookMappings::class;
    }

    public function selectAll(Closure $queryBuilderSetupFn = null): array
    {
        if (!$queryBuilderSetupFn) {
            $queryBuilderSetupFn = function (SelectInterface $select) {
                $t = $this->rootMapInstructor->getTableName();
                $select->from($t . ' AS b');
                $select->leftJoin('notes AS n', 'n.' . $t . 'Id = b.id');
                $select->cols([
                    'b.id as id',
                    'b.title as title',
                    'b.pagecount as pagecount',
                    'n.id as notes.id',
                    'n.content as notes.content',
                    'n.booksId as notes.booksId'
                ]);
            };
        }
        return parent::selectAll($queryBuilderSetupFn);
    }
}
