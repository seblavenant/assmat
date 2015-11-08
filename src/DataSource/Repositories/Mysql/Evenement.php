<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject as DTO;

class Evenement extends AbstractMysql implements Repositories\Evenement
{
    const
        TABLE_NAME = 'evenement';

    private
        $evenementTypeRepository,
        $contratRepository;

    public function __construct(Mysql $db, Repositories\EvenementType $evenementTypeRepository, Repositories\Contrat $contratRepository)
    {
        parent::__construct($db);

        $this->evenementTypeRepository = $evenementTypeRepository;
        $this->contratRepository = $contratRepository;
    }

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findOneFromContratAndDay($contratId, \DateTime $date = null)
    {
        $query = $this->getQueryFromContrat($contratId, $date);

        return $this->fetchOne($query);
    }

    public function findAllFromContrat($contratId, \DateTime $date = null, $fullWeek = false)
    {
        $query = $this->getQueryFromContrat($contratId, $date, $fullWeek);

        return $this->fetchAll($query);
    }

    public function findAllFromContact($contactId, \DateTime $date = null)
    {
        $query = $this->getBaseQuery();

        if($date === null)
        {
            $date = new \DateTime();
        }

        $query->leftJoin('contrat')->on('contrat_id', 'contrat.id');
        $query->leftJoin('employe')->on('contrat.employe_id', 'employe.id');
        $query->leftJoin('employeur')->on('contrat.employeur_id', 'employeur.id');
        $query->leftJoin('contact', 'employe_contact')->on('employe.contact_id', 'employe_contact.id');
        $query->leftJoin('contact', 'employeur_contact')->on('employeur.contact_id', 'employeur_contact.id');

        $dateCondition = $this->buildDateCondition(new \DateTime($date->format('Y-m-01')), new \DateTime($date->format('Y-m-t')));
        $contactCondition = (
            (new Types\Integer('employe_contact.id'))->equal($contactId)
            ->or((new Types\Integer('employeur_contact.id'))->equal($contactId))
        );

        $query->where($dateCondition->and($contactCondition));

        return $this->fetchAll($query);
    }

    private function getQueryFromContrat($contratId, \DateTime $date = null, $fullWeek = false)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('contrat_id'))->equal($contratId));

        if($date !== null)
        {
            $dateDebut = $date;
            if($fullWeek === true)
            {
                $dateDebut = new \DateTime(date('Y-m-d', strtotime($date->format('Y-m') .' last monday of previous month')));
            }

            $dateFin = new \DateTime($date->format('Y-m-t'));
            $query->where($this->buildDateCondition($dateDebut, $dateFin));
        }

        return $query;
    }

    private function buildDateCondition(\DateTime $dateDebut, \DateTime $dateFin)
    {
        return (
            (new Types\Datetime('date'))->greaterOrEqualThan($dateDebut->format('Y-m-d'))
            ->and((new Types\DateTime('date'))->lowerOrEqualThan($dateFin->format('Y-m-d')))
        );
    }

    public function persist(DTO $evenementDTO)
    {
        $this->db->executeQuery('
            INSERT INTO evenement (date, heure_debut, heure_fin, type_id, contrat_id)
            VALUES (:date, :heureDebut, :heureFin, :typeId, :contratId)
            ON DUPLICATE KEY UPDATE
            date = :date, heure_debut = :heureDebut, heure_fin = :heureFin, type_id = :typeId, contrat_id = :contratId
            ',
            array(
                'date' => $evenementDTO->date,
                'heureDebut' => $evenementDTO->heureDebut,
                'heureFin' => $evenementDTO->heureFin,
                'typeId' => $evenementDTO->typeId,
                'contratId' => $evenementDTO->contratId,
            ),
            array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            )
        );
    }

    public function delete($id)
    {
        $this->db->delete(self::TABLE_NAME, array('id' => $id));
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'date', 'heure_debut', 'heure_fin', 'type_id', 'contrat_id')))
            ->from(self::TABLE_NAME)
            ->orderBy('date', OrderBy::ASC)
            ->orderBy('heure_debut', OrderBy::DESC);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'date' => new Fields\NotNullable(new Fields\DateTime('date', 'Y-m-d')),
            'heureDebut' => new Fields\NotNullable(new Fields\DateTime('heure_debut', 'H:i:s')),
            'heureFin' => new Fields\NotNullable(new Fields\DateTime('heure_fin', 'H:i:s')),
            'typeId' => new Fields\NotNullable(new Fields\Integer('type_id')),
            'contratId' => new Fields\NotNullable(new Fields\Integer('contrat_id')),
        );
    }

    public function getDomain(DTO $dto)
    {
        $dto->set('type', function() use($dto) {
            return $this->evenementTypeRepository->find($dto->typeId);
        });

        $dto->set('contrat', function() use($dto) {
            return $this->contratRepository->find($dto->contratId);
        });

        return new Domains\Evenement($dto);
    }

    public function getDTO()
    {
        return new DataTransferObjects\Evenement();
    }
}