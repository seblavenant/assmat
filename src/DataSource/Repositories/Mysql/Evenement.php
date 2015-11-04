<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Query;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject as DTO;
use Assmat\Services\Evenements\Dates\Date;
use Muffin\Conditions\Sets\OrSet;

class Evenement extends AbstractMysql implements Repositories\Evenement
{
    const
        TABLE_NAME = 'evenement';

    private
        $evenementTypeRepository;

    public function __construct(Mysql $db, Repositories\EvenementType $evenementTypeRepository)
    {
        parent::__construct($db);

        $this->evenementTypeRepository = $evenementTypeRepository;
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

    public function findAllFromContact($contactId)
    {
        $query = $this->getBaseQuery();

        $query->leftJoin('contrat')->on('contrat_id', 'contrat.id');
        $query->leftJoin('contact', 'employe_contact')->on('contrat.employe_id', 'employe_contact.id');
        $query->leftJoin('contact', 'employeur_contact')->on('contrat.employeur_id', 'employeur_contact.id');

        $orSet = new OrSet();
        $orSet->add((new Types\Integer('employe_contact.id'))->equal($contactId));
        $orSet->add((new Types\Integer('employeur_contact.id'))->equal($contactId));
        $query->where($orSet);

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
            $this->getDateQuery($query, $dateDebut, $dateFin);
        }

        return $query;
    }

    public function getDateQuery(Query $query, \DateTime $dateDebut, \DateTime $dateFin)
    {
        $query->where((new Types\Datetime('date'))->greaterOrEqualThan($dateDebut->format('Y-m-d')));
        $query->where((new Types\DateTime('date'))->lowerOrEqualThan($dateFin->format('Y-m-d')));

        return $query;
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
            ->select($this->prefixTableFields(array('id', 'date', 'heure_debut', 'heure_fin', 'type_id')))
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
        );
    }

    public function getDomain(DTO $dto)
    {
        $dto->set('type', function() use($dto) {
            return $this->evenementTypeRepository->find($dto->typeId);
        });

        return new Domains\Evenement($dto);
    }

    public function getDTO()
    {
        return new DataTransferObjects\Evenement();
    }
}