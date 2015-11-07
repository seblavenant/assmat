<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Muffin\Query;
use Spear\Silex\Persistence\DTOHydrators;
use Spear\Silex\Persistence\DataTransferObject;

abstract class AbstractMysql
{
    protected
        $db;

    public function __construct(Mysql $db)
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

        $domains = array();
        if(empty($dataSet))
        {
            $dataSet = array();
        }

        foreach($dataSet as $record)
        {
            $domains[] = $this->buildDomainObject($record);
        }

        return $domains;
    }

    protected function buildDomainObject(array $record)
    {
        $dto = $this->buildDTOObject($record);

        if(!$dto instanceof DataTransferObject)
        {
            throw new \Exception('invalid DTO builded !');
        }

        return $this->getDomain($dto);
    }

    protected function buildDTOObject(array $record)
    {
        $hydrator = new DTOHydrators\ByField($this->getFields());
        $dto = $hydrator->hydrate($this->getDTO(), $record);

        return $dto;
    }

    protected function prefixTableFields($fields)
    {
        $prefixTableFields = array_map(
            function($field){
                return $this->prefixTableField($field);
            },
            $fields
        );

        return $prefixTableFields;
    }

    protected function prefixTableField($field)
    {
        return static::TABLE_NAME . '.' . $field;
    }
}