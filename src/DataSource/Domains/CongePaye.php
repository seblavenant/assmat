<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;

class CongePaye
{
    private
        $fields;

    public function __construct(DTO\CongePaye $congePayeDTO)
    {
        $this->fields = $congePayeDTO;
    }

    public function getAcquisNb()
    {
        return $this->fields->acquisNb;
    }

    public function getPrisNb()
    {
        return $this->fields->prisNb;
    }

    public function getRestantNb()
    {
        $reference = $this->getReference();
        $nbJourReference = 0;
        if($reference instanceof CpReference)
        {
            $nbJourReference = $reference->getNbJours();
        }

        return $this->getAcquisNb() + $nbJourReference - $this->getPrisNb();
    }

    public function getReference()
    {
        return $this->fields->load('reference');
    }

    public function toArray()
    {
        return [
            Constants\Lignes\Type::CONGES_PAYES_ACQUIS => $this->getAcquisNb(),
            Constants\Lignes\Type::CONGES_PAYES_PRIS => $this->getPrisNb(),
        ];
    }
}
