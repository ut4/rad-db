<?php

namespace Rad\Db\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Frameowork\ExpectationFailureException;
use Closure;

class BaseTestCase extends PHPUnitTestCase
{
    public function assertCallback(Closure $callbackContainingAssertions)
    {
        return parent::callBack(function () use ($callbackContainingAssertions) {
            try {
                $callbackContainingAssertions(...func_get_args());
                return true;
            } catch (ExpectationFailureException $e) {
                echo $e->getComparisonFailure();
                return false;
            }
        });
    }
}
