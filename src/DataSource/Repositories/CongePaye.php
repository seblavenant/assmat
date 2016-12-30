<?php

namespace Assmat\DataSource\Repositories;

interface CongePaye
{
    public function coundAllFromContratAndWeek($contratId, \DateTime $date);

    public function findFromContratAndDate($contratId, $annee, $mois);
}
