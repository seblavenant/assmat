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
use Spear\Silex\Persistence\DataTransferObject;

class CpReference extends AbstractMysql implements Repositories\CpReference
{
    const
        TABLE_NAME = 'cp_reference';

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

    public function getDomain(DataTransferObject $cpReferenceDTO)
    {
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
