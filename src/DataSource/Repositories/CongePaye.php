<?php

namespace Assmat\DataSource\Repositories;

interface CongePaye
{
    public function findFromContratAndDate($contratId, $annee, $mois);
}
