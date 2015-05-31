<?php

namespace Assmat\DataSource\Repositories\Memory\Evenement;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories;

class Type implements Repositories\EvenementType
{
    private
        $types;

    public function __construct()
    {
        $this->types = array(
            Constants\Evenements\Type::GARDE => (new Types\Garde())->getDomain(),
            Constants\Evenements\Type::CONGE_PAYE => (new Types\CongePaye())->getDomain(),
            Constants\Evenements\Type::ABSENCE_PAYEE => (new Types\AbsencePayee())->getDomain(),
            Constants\Evenements\Type::ABSENCE_NON_PAYEE => (new Types\AbsenceNonPayee())->getDomain(),
            Constants\Evenements\Type::JOUR_FERIE => (new Types\JourFerie())->getDomain(),
        );
    }

    public function find($id)
    {
        if(!array_key_exists($id, $this->types))
        {
            throw new \Exception(sprintf('Le type d\'évenement %d n\'existe pas !', $id));
        }

        return $this->types[$id];
    }

    public function findAll()
    {
        return $this->types;
    }
}