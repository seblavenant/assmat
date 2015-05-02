<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\Iterators;

class Template implements Repositories\LigneTemplate
{
    private
        $lignes;

    public function __construct()
    {
        $this->lignes = array(
            Constants\Lignes\Type::SALAIRE => (new Templates\Salaire())->getDomain(),
            Constants\Lignes\Type::CSG_RDS => (new Templates\CsgRds())->getDomain(),
            Constants\Lignes\Type::CSG_DEDUCTIBLE => (new Templates\CsgDeductible())->getDomain(),
            Constants\Lignes\Type::SECURITE_SOCIALE => (new Templates\SecuriteSociale())->getDomain(),
            Constants\Lignes\Type::RETRAITE_COMPLEMENTAIRE => (new Templates\RetraiteComplementaire())->getDomain(),
            Constants\Lignes\Type::PREVOYANCE => (new Templates\Prevoyance())->getDomain(),
            Constants\Lignes\Type::AGFF => (new Templates\Agff())->getDomain(),
            Constants\Lignes\Type::ASSURANCE_CHOMAGE => (new Templates\AssuranceChomage())->getDomain(),
        );
    }

    public function find($type)
    {
        if(array_key_exists($type, $this->lignes))
        {
            return $this->lignes[$type];
        }
    }

    public function findAll()
    {
        return $this->lignes;
    }

    public function findFromTypes(array $types)
    {
        return new Iterators\Filters\Lignes\Code(new \ArrayIterator($this->lignes), $types);
    }
}