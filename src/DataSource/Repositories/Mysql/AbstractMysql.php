<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Muffin\Query;
use Spear\Silex\Persistence\DTOHydrators;

class AbstractMysql
{
    private
        $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function fetchOne(Query $query)
    {
        $statement = $this->pdo->query($query->toString());

        if($statement === false)
        {
            return null;
        }

        $dataSet = $statement->fetch();

        if($dataSet === false)
        {
            return null;
        }

        return $this->buildDomainObject($dataSet);
    }

    protected function buildDomainObject(array $record)
    {
        $dto = $this->buildDTOObject($record);

        return $this->getDomain($dto);
    }

    protected function buildDTOObject(array $record)
    {
        $hydrator = new DTOHydrators\ByField($this->getFields());
        $dto = $hydrator->hydrate($this->getDTO(), $record);

        return $dto;
    }
}