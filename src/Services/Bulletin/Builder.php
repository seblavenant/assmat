<?php

namespace Assmat\Services\Bulletin;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

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
        // TODO : remplace ligneRepository par lignesCollector
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
            $ligne->compute($bulletin);
        }

        return $bulletin;
    }
}