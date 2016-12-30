<?php

namespace Assmat\DataSource\Repositories\Memory;

use Assmat\DataSource\Repositories;

class CongePaye implements Repositories\CongePaye
{
    public function coundAllFromContratAndWeek($contratId, \DateTime $date)
    {
        return 0;
    }

    public function findFromContratAndDate($contratId, $annee, $mois)
    {
        return [];
    }
}
