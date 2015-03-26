<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Contrat extends Related
{
    public
        $id,
        $employeId,
        $baseHeure,
        $indemnites,
        $type;

    public function __construct()
    {
        parent::__construct(array(
            'bulletins',
        ));
    }
}