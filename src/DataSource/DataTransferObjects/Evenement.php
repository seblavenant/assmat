<?php

namespace Assmat\DataSource\DataTransferObjects;

use Doctrine\DBAL\Driver\Connection;
use Spear\Silex\Persistence\DataTransferObject;

class Evenement implements DataTransferObject
{
    public
        $id,
        $date,
        $heureDebut,
        $heureFin,
        $typeId,
        $contratId;
}