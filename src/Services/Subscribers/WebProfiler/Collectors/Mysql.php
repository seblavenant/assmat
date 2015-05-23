<?php

namespace Assmat\Services\Subscribers\WebProfiler\Collectors;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Assmat\Services\Events;

class Mysql implements EventSubscriberInterface
{
    public function __construct($mysqlCollector)
    {
        $this->mysqlCollector = $mysqlCollector;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'mysql.query' => array('onMysqlQuery')
        );
    }

    public function onMysqlQuery(Events\Mysql $mysql)
    {
        $this->mysqlCollector->add($mysql);
    }
}