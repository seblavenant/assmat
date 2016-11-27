<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Repositories;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;
use Muffin\Types\Integer;
use Muffin\Queries;
use Muffin\Tests\Escapers\SimpleEscaper;
use Assmat\Services\Lignes\Computers\CpAnneeReference;
use Spear\Silex\Persistence\Fields;
use Assmat\DataSource\Constants;

class CpReference extends AbstractMysql implements Repositories\CpReference
{
    const
        TABLE_NAME = 'cp_reference';

    private
        $ligneRepository;
        
    public function __construct(Mysql $db, Repositories\Ligne $ligneRepository)
    {
        parent::__construct($db);

        $this->ligneRepository = $ligneRepository;
    }

    public function persist(DTO\CpReference $cpReferenceDTO)
    {
        $this->db->executeQuery('
            INSERT INTO cp_reference (annee, taux_journalier, nb_jours, contrat_id)
            VALUES (:annee, :tauxJournalier, :nbJours, :contratId)
            ON DUPLICATE KEY UPDATE
            annee = :annee, taux_journalier = :tauxJournalier, nb_jours = :nbJours, contrat_id = :contratId
            ',
            array(
                'annee' => $cpReferenceDTO->annee,
                'tauxJournalier' => $cpReferenceDTO->tauxJournalier,
                'nbJours' => $cpReferenceDTO->nbJours,
                'contratId' => $cpReferenceDTO->contratId,
            ),
            array(
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            )
        );
    }

    private function getDetails($contratId, $anneeReference)
    {
        $congesPayes = $this->ligneRepository->countAllFromContratAndContext(
            $contratId,
            Constants\Lignes\Context::CONGE_PAYE,
            new \DateTime(sprintf('%d-%d', $anneeReference + 2, 5)),
            new \DateTime(sprintf('%d-%d', $anneeReference + 1, 6))
        );

        $cpReference = $this->findOneFromContratAndDate($contratId,$anneeReference);

        $cpAcquisReference = 0;
        if($cpReference instanceof Domains\CpReference)
        {
            $cpAcquisReference = $cpReference->getNbJours();
        }

        $cpAcquis = 0;
        if(isset($congesPayes[Constants\Lignes\Type::CONGES_PAYES_ACQUIS]))
        {
            $cpAcquis = $congesPayes[Constants\Lignes\Type::CONGES_PAYES_ACQUIS];
        }
        
        $cpPris = 0;
        if(isset($congesPayes[Constants\Lignes\Type::CONGES_PAYES_PRIS]))
        {
            $cpPris = $congesPayes[Constants\Lignes\Type::CONGES_PAYES_PRIS];
        }

        return [
            Constants\Lignes\Type::CONGES_PAYES_ACQUIS_REFERENCE => $cpAcquisReference,
            Constants\Lignes\Type::CONGES_PAYES_ACQUIS => $cpAcquis,
            Constants\Lignes\Type::CONGES_PAYES_PRIS => $cpPris,
            Constants\Lignes\Type::CONGES_PAYES_RESTANT => $cpAcquisReference + $cpAcquis - $cpPris,
        ];
    }

    public function findOneFromContratAndDate($contratId, $anneeReference = null)
    {
        $query = $this->getBaseQuery()
            ->where((new Integer('contrat_id'))->equal($contratId));

        if($anneeReference === null)
        {
            $anneeReference = (new CpAnneeReference())->compute();
        }

        $query->where((new Integer('annee'))->equal($anneeReference));

        return $this->fetchOne($query);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'annee', 'taux_journalier', 'nb_jours', 'contrat_id')))
            ->from(self::TABLE_NAME)
        ;

        return $query;
    }

    public function getDomain(DTO\CpReference $cpReferenceDTO)
    {
        $cpReferenceDTO->set('details', function() use($cpReferenceDTO ) {
            return $this->getDetails($cpReferenceDTO->contratId, $cpReferenceDTO->annee);
        });

        return new Domains\CpReference($cpReferenceDTO);
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'annee' => new Fields\NotNullable(new Fields\UnsignedInteger('annee')),
            'tauxJournalier' => new Fields\NotNullable(new Fields\Float('taux_journalier')),
            'nbJours' => new Fields\NotNullable(new Fields\Float('nb_jours')),
            'contratId' => new Fields\NotNullable(new Fields\UnsignedInteger('contrat_id'))
        );
    }

    public function getDTO()
    {
        return new DTO\CpReference();
    }
}
