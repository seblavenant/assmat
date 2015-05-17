<?php

namespace Assmat\DataSource\Repositories;

use Assmat\Services\Evenements\Periods\Period;

interface Evenement
{
    public function find($id);

    public function findOneFromContratAndDay($contratId, \DateTime $date = null);

    public function findAllFromContrat($contratId, Period $period = null);
}