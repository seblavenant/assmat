<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Contrat implements DataTransferObject
{
    public
        $id,
        $employeId,
        $baseHeure,
        $indemnites,
        $type;
}