<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Ligne implements DataTransferObject
{
    public
        $id,
        $code,
        $type,
        $taux,
        $quantite,
        $valeur,
        $hydrateFromBulletinClosure;
}