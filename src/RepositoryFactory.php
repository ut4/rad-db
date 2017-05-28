<?php

namespace Rad\Db;

use PDO;
use Aura\SqlQuery\QueryFactory;
use InvalidArgumentException;

class RepositoryFactory
{
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function usePdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $repositoryClass
     * @return AutoRepository
     * @throws InvalidArgumentException
     */
    public function createAutoRepository(string $repositoryClass): AutoRepository
    {
        if (!\is_subclass_of($repositoryClass, AutoRepository::class)) {
            throw new InvalidArgumentException(
                $repositoryClass . ' should extend ' . AutoRepository::class
            );
        }
        return new $repositoryClass(
            new Planner(),
            new PlanExecutor($this->createQueryBuildingDb())
        );
    }

    /**
     * @return QueryBuildingDb
     */
    private function createQueryBuildingDb(): QueryBuildingDb
    {
        return new QueryBuildingDb(
            new Db(new Connection($this->pdo)),
            new QueryFactory($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME))
        );
    }
}
