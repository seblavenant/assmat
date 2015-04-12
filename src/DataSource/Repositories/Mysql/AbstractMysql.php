<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Muffin\Query;
use Spear\Silex\Persistence\DTOHydrators;
use Doctrine\DBAL\Driver\Connection;
use Spear\Silex\Persistence\DataTransferObject;

abstract class AbstractMysql
{
    protected
        $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    abstract public function getDomain(DataTransferObject $dto);

    abstract public function getFields();

    abstract public function getDTO();

    protected function fetchOne(Query $query)
    {
        $record = $this->db->fetchAssoc($query->toString());

        if($record === false)
        {
            return null;
        }

        return $this->buildDomainObject($record);
    }

    protected function fetchAll(Query $query)
    {
        $dataSet = $this->db->fetchAll($query->toString());

        if(empty($dataSet))
        {
            return null;
        }

        $domains = array();
        foreach($dataSet as $record)
        {
            $domains[] = $this->buildDomainObject($record);
        }

        return $domains;
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