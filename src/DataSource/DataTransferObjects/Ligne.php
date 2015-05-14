<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Ligne implements DataTransferObject
{
    public
        $id,
        $label,
        $type,
        $action,
        $context,
        $base,
        $taux,
        $quantite,
        $valeur,
        $computeClosure;
}