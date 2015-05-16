<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Contrat extends Related
{
    public
        $id,
        $nom,
        $salaireHoraire,
        $joursGarde,
        $heuresHebdo,
        $nombreSemainesAn,
        $indemnites,
        $typeId,
        $employeId,
        $employeurId;

    public function __construct()
    {
        parent::__construct(array(
            'indemnites',
            'employe',
            'employeur'
        ));
    }
}