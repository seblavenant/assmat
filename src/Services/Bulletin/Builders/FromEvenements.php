<?php

namespace Assmat\Services\Bulletin\Builders;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\Services\Providers\ServiceProvider;

class FromEvenements
{
    private
        $ligneTemplateRepository,
        $ligneBuilderProvider;

    public function __construct(Repositories\LigneTemplate $ligneTemplateRepository, ServiceProvider $ligneBuilderProvider)
    {
        $this->ligneTemplateRepository = $ligneTemplateRepository;
        $this->ligneBuilderProvider = $ligneBuilderProvider;
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

        $bulletin->compute();

        foreach($lignes as $ligne)
        {
            $ligneBuilder = $this->ligneBuilderProvider->get($ligne->getTypeId());
            $ligneBuilder()->compute($ligne, $bulletin);
        }

        return $bulletin;
    }
}