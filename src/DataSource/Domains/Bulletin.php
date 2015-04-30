<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Bulletin
{
    private
        $fields,
        $salaire;

    public function __construct(DTO\Bulletin $bulletinDTO)
    {
        $this->fields = $bulletinDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getAnnee()
    {
        return $this->fields->annee;
    }

    public function getMois()
    {
        return $this->fields->mois;
    }

    public function getLignes()
    {
        return $this->fields->load('lignes');
    }

    public function getEvenements()
    {
        return $this->fields->load('evenements');
    }

    public function getContrat()
    {
        return $this->fields->load('contrat');
    }

    public function setSalaire(Ligne $salaire)
    {
        $this->salaire = $salaire;
    }

    public function getSalaire()
    {
        return $this->salaire;
    }
}