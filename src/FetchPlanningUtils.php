<?php

namespace Rad\Db;

class FetchPlanningUtils
{
    /**
     * @return bool
     */
    public function targetColumnExists(
        array $columns,
        string $targetPropertyName
    ): bool {
        foreach ($columns as $column) {
            if (\strpos($column, $targetPropertyName . '.') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function collectTargetColumns(
        array $columns,
        string $targetPropertyName
    ): array {
        $columnsToInclude = [];
        $level = strlen($targetPropertyName) ? \substr_count($targetPropertyName, '.') + 1: 0;
        foreach ($columns as $tablePath => $aliasPath) {
            $pieces = \explode('.', $aliasPath);
            if (
                // Second+ levels, select columns under $targetProp
                ($level > 0 && $pieces[$level - 1] === $targetPropertyName) ||
                // First level, select root columns only
                ($level < 1 && \count($pieces) === 1)
            ) {
                $columnsToInclude[$tablePath] = $level > 0
                    ? '"' . $aliasPath . '"'
                    : $aliasPath;
            }
        }
        return $columnsToInclude;
    }
}
