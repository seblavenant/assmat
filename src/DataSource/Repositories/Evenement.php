<?php

namespace Assmat\DataSource\Repositories;

interface Evenement
{
    public function find($id);

    public function findOneFromContratAndDay($contratId, \DateTime $date = null);

    public function findAllFromContrat($contratId, \DateTime $date = null, $fullWeek = false);

    public function findAllFromContact($contactId, \DateTime $date = null);
}