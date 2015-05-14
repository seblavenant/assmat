<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Contrat extends Related
{
    public
        $id,
        $nom,
        $employeId,
        $salaireHoraire,
        $joursGarde,
        $heuresHebdo,
        $nombreSemainesAn,
        $indemnites,
        $typeId;

    public function __construct()
    {
        parent::__construct(array(
            'indemnites',
        ));
    }
}