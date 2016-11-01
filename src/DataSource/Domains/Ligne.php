<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

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

    public function getTypeId()
    {
        return $this->fields->typeId;
    }

    public function getLabel()
    {
        return $this->fields->label;
    }

    public function getActionId()
    {
        return $this->fields->actionId;
    }

    public function getContextId()
    {
        return $this->fields->contextId;
    }

    public function setBase($base)
    {
        $this->fields->base = $base;

        return $this;
    }

    public function getBase()
    {
        return $this->fields->base;
    }

    public function setQuantite($quantite)
    {
        if($this->isTaux())
        {
            $quantite = $quantite / 100;
        }

        $this->fields->quantite = $quantite;

        return $this;
    }

    public function getQuantite()
    {
        $quantite = $this->fields->quantite;
        if($this->isTaux())
        {
            $quantite = $quantite * 100;
        }
        
        return $quantite;
    }

    public function computeValeur()
    {
        $this->fields->valeur = round($this->fields->quantite * $this->fields->base, 2);

        return $this;
    }

    public function getValeur()
    {
        return $this->fields->valeur;
    }

    public function setBulletinId($bulletinId)
    {
        $this->fields->bulletinId = $bulletinId;
    }

    public function getBulletinId()
    {
        return $this->fields->bulletinId;
    }
    
    public function isBaseEditable()
    {
        return (bool) $this->fields->baseEditable;
    }
    
    public function isQuantiteEditable()
    {
        return (bool) $this->fields->quantiteEditable;
    }
    
    public function isTaux()
    {
        return (bool) $this->fields->taux;
    }

    public function persist(Repositories\Ligne $ligneRepository)
    {
        if($this->fields->id !== null)
        {
            return $ligneRepository->update($this->fields);
        }

        return $ligneRepository->create($this->fields);
    }
    
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'typeId' => $this->getTypeId(),
            'actionId' => $this->getActionId(),
            'contextId' => $this->getContextId(),
            'base' => number_format($this->getBase(), 2, '.', ''),
            'quantite' => number_format($this->getQuantite(), 2, '.', ''),
            'valeur' => number_format($this->getValeur(), 2, '.', ''),
            'bulletinId' => $this->getBulletinId(),
            'isBaseEditable' => $this->isBaseEditable(),
            'isQuantiteEditable' => $this->isQuantiteEditable(),
            'isTaux' => $this->isTaux(),
        ];
    }
}