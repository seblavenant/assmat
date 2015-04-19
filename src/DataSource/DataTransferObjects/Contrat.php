<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Contrat implements DataTransferObject
{
    public
        $id,
        $nom,
        $employeId,
        $baseHeure,
        $indemnites,
        $type;
}