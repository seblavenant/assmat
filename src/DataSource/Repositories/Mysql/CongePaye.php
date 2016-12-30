<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Services\Lignes\Computers\CpAnneeReference;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Queries\Select;
use Assmat\DataSource\Constants\Evenements\Type;

class CongePaye implements Repositories\CongePaye
{
    private
        $ligneRepository,
        $cpReferenceRepository,
        $db;

    public function __construct(Mysql $db, Repositories\Ligne $ligneRepository, Repositories\CpReference $cpReferenceRepository)
    {
        $this->db = $db;
        $this->ligneRepository = $ligneRepository;
        $this->cpReferenceRepository = $cpReferenceRepository;
    }

    public function coundAllFromContratAndWeek($contratId, \DateTime $date)
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper());
        $query
            ->select('count(*) as count')
            ->from('evenement')
            ->where((new Types\String('YEARWEEK(date)'))->equal($date->format('YW')))
            ->where((new Types\Integer('type_id'))->equal(Type::CONGE_PAYE))
            ->where((new Types\Integer('contrat_id'))->equal($contratId));

        return ((int) $this->db->fetchColumn($query->toString()));
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
