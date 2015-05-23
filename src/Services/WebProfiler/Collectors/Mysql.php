<?php

namespace Assmat\Services\WebProfiler\Collectors;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Assmat\Services\Events;

class Mysql extends DataCollector
{
    public function __construct()
    {
        $this->data = array();
    }

    public function collect(Request $request, Response $response, \Exception $exception = null){}

    public function add(Events\Mysql $mysql)
    {
        $this->data[] = $mysql->getQuery();
    }

    public function getData()
    {
        return $this->data;
    }

    public function getName()
    {
        return 'mysql';
    }
}
