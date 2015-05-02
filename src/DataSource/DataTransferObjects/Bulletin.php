<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Bulletin extends Related
{
    public
        $id,
        $mois,
        $annee,
        $contratId,
        $lignes;

    public function __construct()
    {
        parent::__construct(array(
            'evenements',
            'contrat',
            'lignes',
        ));
    }
}