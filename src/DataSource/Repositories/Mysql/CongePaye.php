<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Services\Lignes\Computers\CpAnneeReference;

class CongePaye implements Repositories\CongePaye
{
    private
        $ligneRepository,
        $cpReferenceRepository;

    public function __construct(Repositories\Ligne $ligneRepository, Repositories\CpReference $cpReferenceRepository)
    {
        $this->ligneRepository = $ligneRepository;
        $this->cpReferenceRepository = $cpReferenceRepository;
    }

    public function findFromContratAndDate($contratId, $annee, $mois)
    {
        $anneeDebut = ($mois >= 6) ? $annee : $annee - 1;

        $dateFin = new \DateTime(sprintf('%d-%d', $annee, $mois));
        $dateDebut = new \DateTime(sprintf('%d-%d', $anneeDebut, 6));

        $congesPayesLignes = $this->ligneRepository->countAllFromContratAndContext($contratId, Constants\Lignes\Context::CONGE_PAYE, $dateFin, $dateDebut);

        $dto = new DTO\CongePaye();
        $dto->acquisNb = $congesPayesLignes[Constants\Lignes\Type::CONGES_PAYES_ACQUIS];
        $dto->prisNb = $congesPayesLignes[Constants\Lignes\Type::CONGES_PAYES_PRIS];

        $dto->set('reference', function() use($contratId, $annee, $mois) {
            return $this->cpReferenceRepository->findOneFromContratAndDate($contratId, (new CpAnneeReference())->compute($annee, $mois));
        });

        return new Domains\CongePaye($dto);
    }
}
