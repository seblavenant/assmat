<?php

namespace Assmat\DataSource\Repositories;

use Assmat\Services\Evenements\Dates\Date;

interface Evenement
{
    public function find($id);

    public function findOneFromContratAndDay($contratId, \DateTime $date = null);

    public function findAllFromContrat($contratId, Date $period = null);
}