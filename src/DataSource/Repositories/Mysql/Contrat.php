<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject;

class Contrat extends AbstractMysql implements Repositories\Contrat
{
    const
        TABLE_NAME = 'contrat';

    private
        $indemniteRepository,
        $employeRepository,
        $employeurRepository;

    public function __construct(Mysql $db, Repositories\Indemnite $indemniteRepository, Repositories\Employe $employeRepository, Repositories\Employeur $employeurRepository)
    {
        parent::__construct($db);

        $this->indemniteRepository = $indemniteRepository;
        $this->employeRepository = $employeRepository;
        $this->employeurRepository = $employeurRepository;
    }

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromEmploye($employeId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('employe_id'))->equal($employeId));

        return $this->fetchAll($query);
    }

    public function findFromEmployeur($employeurId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('employeur_id'))->equal($employeurId));

        return $this->fetchAll($query);
    }

    public function findFromContact($contactId)
    {
        $query = $this->getBaseQuery();
        $query->leftJoin('employe')->on('contrat.employe_id', 'employe.id');
        $query->leftJoin('employeur')->on('contrat.employeur_id', 'employeur.id');
        $query->leftJoin('contact', 'employe_contact')->on('employe.contact_id', 'employe_contact.id');
        $query->leftJoin('contact', 'employeur_contact')->on('employeur.contact_id', 'employeur_contact.id');

        $contactCondition = (
            (new Types\Integer('employe_contact.id'))->equal($contactId)
            ->or((new Types\Integer('employeur_contact.id'))->equal($contactId))
        );
        $query->where($contactCondition);

        return $this->fetchAll($query);
    }

    public function persist(DTO\Contrat $contratDTO)
    {
        if($contratDTO->id !== null)
        {
            return $this->update($contratDTO);
        }

        return $this->create($contratDTO);
    }

    private function update(DTO\Contrat $contratDTO)
    {
        $this->db->update(
            self::TABLE_NAME,
            array(
                'nom' => $contratDTO->nom,
                'salaire_horaire' => $contratDTO->salaireHoraire,
            ),
            array(
                'id' => $contratDTO->id,
            ),
            array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
            )
        );
    }

    private function create(DTO\Contrat $contratDTO)
    {
        $this->db->insert(
            self::TABLE_NAME,
            array(
                'nom' => $contratDTO->nom,
                'salaire_horaire' => $contratDTO->salaireHoraire,
                'jours_garde' => $contratDTO->joursGarde,
                'heures_hebdo' => $contratDTO->heuresHebdo,
                'nb_semaines_an' => $contratDTO->nombreSemainesAn,
                'type_id' => $contratDTO->typeId,
                'employe_id' => $contratDTO->employeId,
                'employeur_id' => $contratDTO->employeurId,
            ),
            array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            )
        );

        $contratDTO->id = (int) $this->db->lastInsertId();

        $indemnites = $contratDTO->load('indemnites');
        foreach($indemnites as $indemnites)
        {
            $indemnites->setContratId($contratDTO->id);
            $indemnites->persist($this->indemniteRepository);
        }

        return new Domains\Contrat($contratDTO);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'nom', 'salaire_horaire', 'jours_garde', 'heures_hebdo', 'type_id', 'employe_id', 'employeur_id')))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'nom' => new Fields\NotNullable(new Fields\String('nom')),
            'salaireHoraire' => new Fields\NotNullable(new Fields\Float('salaire_horaire')),
            'joursGarde' => new Fields\NotNullable(new Fields\Integer('jours_garde')),
            'heuresHebdo' => new Fields\NotNullable(new Fields\Integer('heures_hebdo')),
            'typeId' => new Fields\NotNullable(new Fields\Integer('type_id')),
            'employeId' => new Fields\NotNullable(new Fields\Integer('employe_id')),
            'employeurId' => new Fields\NotNullable(new Fields\Integer('employeur_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('indemnites', function() use($dto) {
            return $this->indemniteRepository->findFromContrat($dto->id);
        });

        $dto->set('employe', function() use($dto) {
            return $this->employeRepository->find($dto->employeId);
        });

        $dto->set('employeur', function() use($dto) {
            return $this->employeurRepository->find($dto->employeurId);
        });

        return new Domains\Contrat($dto);
    }

    public function getDTO()
    {
        return new DTO\Contrat();
    }
}