<?php

namespace Assmat\Services\Subscribers\WebProfiler\Collectors;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Assmat\Services\Events;
use Assmat\Services\WebProfiler\Collectors;

class Mysql implements EventSubscriberInterface
{
    private
        $mysqlCollector;

    public function __construct(Collectors\Mysql $mysqlCollector)
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