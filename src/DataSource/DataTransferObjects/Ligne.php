<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Ligne implements DataTransferObject
{
    public
        $id,
        $label,
        $typeId,
        $actionId,
        $contextId,
        $base,
        $quantite,
        $valeur,
        $bulletinId,
        $baseEditable,
        $quantiteEditable,
        $taux;
}