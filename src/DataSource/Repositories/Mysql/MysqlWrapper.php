<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Doctrine\DBAL\Driver\Connection;

class MysqlWrapper implements Mysql
{
    private
        $mysql;

    public function __construct(\Doctrine\DBAL\Connection $mysql)
    {
        $this->mysql = $mysql;
    }

    public function fetchAssoc($statement, array $params = array(), array $types = array())
    {
        return $this->mysql->fetchAssoc($statement, $params, $types);
    }

    public function fetchArray($statement, array $params = array(), array $types = array())
    {
        return $this->mysql->fetchArray($statement, $params, $types);
    }

    public function fetchColumn($statement, array $params = array(), $column = 0, array $types = array())
    {
        return $this->mysql->fetchColumn($statement, $params, $column, $types);
    }

    public function fetchAll($sql, array $params = array(), $types = array())
    {
        return $this->mysql->fetchAll($sql, $params, $types);
    }

    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        return $this->mysql->executeQuery($query, $params, $types, $qcp);
    }

    public function delete($tableExpression, array $identifier, array $types = array())
    {
        return $this->mysql->delete($tableExpression, $identifier, $types);
    }

    public function update($tableExpression, array $data, array $identifier, array $types = array())
    {
        return $this->mysql->update($tableExpression, $data, $identifier, $types);
    }

    public function insert($tableExpression, array $data, array $types = array())
    {
        return $this->mysql->insert($tableExpression, $data, $types);
    }

    public function prepare($prepareString)
    {
        return $this->mysql->prepare($prepareString);
    }

    public function query()
    {
        return $this->mysql->query();
    }

    public function quote($input, $type = \PDO::PARAM_STR)
    {
        return $this->mysql->quote($input, $type);
    }

    public function exec($statement)
    {
        return $this->mysql->exec($statement);
    }

    public function lastInsertId($name = null)
    {
        return $this->mysql->lastInsertId($name = null);
    }

    public function beginTransaction()
    {
        return $this->mysql->beginTransaction();
    }

    public function commit()
    {
        return $this->mysql->commit();
    }

    public function rollBack()
    {
        return $this->mysql->rollBack();
    }

    public function errorCode()
    {
        return $this->mysql->errorCode();
    }

    public function errorInfo()
    {
        return $this->mysql->errorInfo();
    }
}