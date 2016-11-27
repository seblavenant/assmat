<?php

namespace Assmat\Services\Bulletin\Builders;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\Services\Providers\ServiceProvider;
use Assmat\Services\Lignes\Computers\CpAnneeReference;


class FromEvenements
{
    private
        $ligneTemplateRepository,
        $cpReferenceRepository,
        $ligneBuilderProvider;

    public function __construct(Repositories\LigneTemplate $ligneTemplateRepository, Repositories\CpReference $cpReferenceRepository, ServiceProvider $ligneBuilderProvider)
    {
        $this->ligneTemplateRepository = $ligneTemplateRepository;
        $this->cpReferenceRepository = $cpReferenceRepository;
        $this->ligneBuilderProvider = $ligneBuilderProvider;
    }

    public function build(Domains\Contrat $contrat, array $evenements, $annee, $mois)
    {
        $contratId = $contrat->getId();
        $lignes = $this->ligneTemplateRepository->findAll();

        $cpReference = $this->cpReferenceRepository->findOneFromContratAndDate($contratId, (new CpAnneeReference())->compute($annee, $mois));
        $congesPayes = null;
        if($cpReference instanceof Domains\CpReference)
        {
            $congesPayes = $cpReference->getDetails();
        }
        
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