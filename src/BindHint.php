<?php

namespace Rad\Db;

interface BindHint
{
    /**
     * @return string
     */
    public function getTargetPropertyName(): string;

    /**
     * @return string
     */
    public function getMapInstructorClassPath(): string;
}
