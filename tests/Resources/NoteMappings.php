<?php

namespace Rad\Db\Resources;

use Rad\Db\Mappable;
use Rad\Db\Resources\Note;

class NoteMappings implements Mappable
{
    public function getTableName(): string
    {
        return 'notes';
    }

    public function getEntityClassPath(): string
    {
        return Note::class;
    }

    public function getIdColumnName(): string
    {
        return 'id';
    }

    public function getBindHints(): array
    {
        return [];
    }
}
