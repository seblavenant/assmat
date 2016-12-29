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
        $congePayeRepository,
        $ligneBuilderProvider;

    public function __construct(Repositories\LigneTemplate $ligneTemplateRepository, Repositories\CongePaye $congePayeRepository, ServiceProvider $ligneBuilderProvider)
    {
        $this->ligneTemplateRepository = $ligneTemplateRepository;
        $this->congePayeRepository = $congePayeRepository;
        $this->ligneBuilderProvider = $ligneBuilderProvider;
    }

    public function build(Domains\Contrat $contrat, array $evenements, $annee, $mois)
    {
        $contratId = $contrat->getId();
        $lignes = $this->ligneTemplateRepository->findAll();

        $congesPayes = $this->congePayeRepository->findFromContratAndDate($contrat->getId(), $annee, $mois);

        $bulletinDTO = new DTO\Bulletin();
        $bulletinDTO->annee = (int) $annee;
        $bulletinDTO->mois = (int) $mois;
        $bulletinDTO->contratId = $contratId;
        $bulletinDTO->set('evenements', $evenements);
        $bulletinDTO->set('contrat', $contrat);
        $bulletinDTO->set('lignes', $lignes);
        $bulletinDTO->set('congesPayes', $congesPayes);
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