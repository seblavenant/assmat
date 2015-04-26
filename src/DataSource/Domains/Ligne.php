<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;

class Ligne
{
    private
        $fields;

    public function __construct(DTO\Ligne $ligneDTO)
    {
        $this->fields = $ligneDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getCode()
    {
        return $this->fields->code;
    }

    public function getType()
    {
        return $this->fields->type;
    }

    public function getTaux()
    {
        return $this->fields->taux;
    }

    public function getQuantite(Domains\Bulletin $bulletin)
    {
        if($this->fields->quantite !== null)
        {
            return $this->fields->quantite;
        }

        return $this->hydrateFieldsFromBulletin($bulletin);
    }

    public function getValeur(Domains\Bulletin $bulletin)
    {
        if($this->fields->valeur !== null)
        {
            return $this->fields->valeur;
        }

        return $this->hydrateFieldsFromBulletin($bulletin);
    }

    private function hydrateFieldsFromBulletin(Domains\Bulletin $bulletin)
    {
        $hydrateFromBulletinClosure = $this->fields->hydrateFromBulletinClosure;
        $hydrateFromBulletinClosure($bulletin);
    }
}