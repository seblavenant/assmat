<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class CongePaye extends Related
{
    public
        $acquisNb,
        $prisNb;
    
    public function __construct()
    {
        parent::__construct([
            'reference'
        ]);
    }
}