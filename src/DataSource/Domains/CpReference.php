<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class CpReference
{
    private
        $fields;

    public function __construct(DTO\CpReference $cpReferenceDTO)
    {
        $this->fields = $cpReferenceDTO;
    }

    public function getAnnee()
    {
        return $this->fields->annee;
    }

    public function getTauxJournalier()
    {
        return $this->fields->tauxJournalier;
    }

    public function getNbJours()
    {
        return $this->fields->nbJours;
    }

    public function getContratId()
    {
        return $this->fields->contratId;
    }
}
