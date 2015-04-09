<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Evenement implements DataTransferObject
{
    public
        $id,
        $date,
        $heureDebut,
        $heureFin,
        $type,
        $contratId;
}