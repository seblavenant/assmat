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

    public function build(Domains\Contrat $contrat, array $evenements)
    {
        $lignes = $this->ligneTemplateRepository->findAll();

        $bulletinDTO = new DTO\Bulletin();
        $bulletinDTO->set('evenements', $evenements);
        $bulletinDTO->set('contrat', $contrat);
        $bulletinDTO->set('lignes', $lignes);
        $bulletin = new Domains\Bulletin($bulletinDTO);

        foreach($bulletin->getEvenements() as $evenement)
        {
            $evenement->computeFromType();

            $bulletin->addHeuresPayees($this->computeHeuresPayees($evenement, $contrat));
            $bulletin->addHeuresNonPayees(! $evenement->isJourPaye() ? $contrat->getHeuresJour() : null);
            $bulletin->addJourGarde($evenement->isJourGarde() ? 1 : 0);
            $bulletin->addCongePaye($evenement->isCongePaye() ? 1 : 0);
        }

        $this->hydrateLignes($lignes, Constants\Lignes\Context::REMUNERATION, $bulletin);
        $this->hydrateLignes($lignes, Constants\Lignes\Context::COTISATION, $bulletin);
        $this->hydrateLignes($lignes, Constants\Lignes\Context::INDEMNITE, $bulletin);

        return $bulletin;
    }

    private function computeHeuresPayees($evenement, $contrat)
    {
        if(! $evenement->isJourPaye())
        {
            return;
        }

        if(! $evenement->getType()->isDureeFixe())
        {
            return $evenement->getDuree()->format('%h') + $evenement->getDuree()->format('%i') / 60;
        }

        return $contrat->getHeuresJour();
    }

    private function hydrateLignes($lignes, $context, $bulletin)
    {
        $lignesContext = new FilterIterator\Lignes\Action(new \ArrayIterator($lignes), $context);

        foreach($lignesContext as $ligne)
        {
            $ligne->compute($bulletin);
        }
    }

}