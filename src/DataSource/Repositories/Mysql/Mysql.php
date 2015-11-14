<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Cache\QueryCacheProfile;

interface Mysql extends Connection
{
    public function fetchAssoc($statement, array $params = array(), array $types = array());

    public function fetchArray($statement, array $params = array(), array $types = array());

    public function fetchColumn($statement, array $params = array(), $column = 0, array $types = array());

    public function fetchAll($sql, array $params = array(), $types = array());

    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null);

    public function delete($tableExpression, array $identifier, array $types = array());

    public function update($tableExpression, array $data, array $identifier, array $types = array());

    public function insert($tableExpression, array $data, array $types = array());
}