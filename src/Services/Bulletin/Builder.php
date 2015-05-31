<?php

namespace Assmat\Services\Bulletin;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Iterators\Filters as FilterIterator;
use Assmat\DataSource\Repositories\Memory\Lignes\Salaire;

class Builder
{
    private
        $ligneTemplateRepository;

    public function __construct(Repositories\LigneTemplate $ligneTemplateRepository)
    {
        $this->ligneTemplateRepository = $ligneTemplateRepository;
    }

    public function build(Domains\Contrat $contrat, array $evenements, $annee, $mois)
    {
        $lignes = $this->ligneTemplateRepository->findAll();

        $bulletinDTO = new DTO\Bulletin();
        $bulletinDTO->annee = (int) $annee;
        $bulletinDTO->mois = (int) $mois;
        $bulletinDTO->contratId = $contrat->getId();
        $bulletinDTO->set('evenements', $evenements);
        $bulletinDTO->set('contrat', $contrat);
        $bulletinDTO->set('lignes', $lignes);
        $bulletinDTO->set('congesPayes', null);
        $bulletin = new Domains\Bulletin($bulletinDTO);

        foreach($bulletin->getEvenements() as $evenement)
        {
            $evenement->computeFromType();

            $this->setContratType($evenement, $contrat);
            $bulletin->addHeuresPayees($this->computeHeuresPayees($evenement, $contrat));
            $bulletin->addHeuresNonPayees(!$evenement->isJourPaye() ? $contrat->getHeuresJour() : null);
            $bulletin->addJourGarde($evenement->isJourGarde() ? 1 : 0);
            $bulletin->addCongePaye($evenement->isCongePaye() ? 1 : 0);
        }

        $this->hydrateLignes($lignes, Constants\Lignes\Context::REMUNERATION, $bulletin);
        $this->hydrateLignes($lignes, Constants\Lignes\Context::COTISATION, $bulletin);
        $this->hydrateLignes($lignes, Constants\Lignes\Context::INDEMNITE, $bulletin);
        $this->hydrateLignes($lignes, Constants\Lignes\Context::CONGE_PAYE, $bulletin);

        return $bulletin;
    }

    private function computeHeuresPayees(Domains\Evenement $evenement, Domains\Contrat $contrat)
    {
        if(!$evenement->isJourPaye())
        {
            return;
        }

        if(!$evenement->getType()->isDureeFixe())
        {
            return $this->computeHeuresEvenement($evenement);
        }

        return $contrat->getHeuresJour();
    }

    private function computeHeuresEvenement(Domains\Evenement $evenement)
    {
        return (int) $evenement->getDuree()->format('%h') + ((int) $evenement->getDuree()->format('%i') / 60);
    }

    private function hydrateLignes($lignes, $context, Domains\Bulletin $bulletin)
    {
        $lignesContext = new FilterIterator\Lignes\Action(new \ArrayIterator($lignes), $context);

        foreach($lignesContext as $ligne)
        {
            $ligne->compute($bulletin);
        }
    }

    private function setContratType(Domains\Evenement $evenement, Domains\Contrat $contrat)
    {
        if(! $evenement->isJourPaye())
        {
            $contrat->setTypeId(Constants\Contrats\Salaire::HEURES);
        }
    }
}