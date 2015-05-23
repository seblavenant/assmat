<?php

namespace Assmat\Services\Events;

use Symfony\Component\EventDispatcher\Event;

class Mysql extends Event
{
    private
        $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
