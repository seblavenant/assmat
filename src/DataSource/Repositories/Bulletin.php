<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface Bulletin extends Repository
{
    public function find($id);

    public function findFromContrat($contratId);

    public function findOneFromContratAndDate($contratId, $annee, $mois)

    public function create(DTO\Bulletin $bulletinDTO);
}