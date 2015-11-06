<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Evenement extends Related
{
    public
        $id,
        $date,
        $heureDebut,
        $heureFin,
        $typeId,
        $contratId;

    public function __construct()
    {
        parent::__construct(array(
            'type',
            'contrat',
        ));
    }
}