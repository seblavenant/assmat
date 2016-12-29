<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject;

class Bulletin extends AbstractMysql implements Repositories\Bulletin
{
    const
        TABLE_NAME = 'bulletin';

    private
        $evenementRepository,
        $contratRepository,
        $ligneRepository,
        $congePayeRepository;

    public function __construct(
        Mysql $db,
        Repositories\Evenement $evenementRepository,
        Repositories\Contrat $contratRepository,
        Repositories\Ligne $ligneRepository,
        Repositories\CongePaye $congePayeRepository
    )
    {
        parent::__construct($db);

        $this->evenementRepository = $evenementRepository;
        $this->contratRepository = $contratRepository;
        $this->ligneRepository = $ligneRepository;
        $this->congePayeRepository = $congePayeRepository;
    }

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromContrat($contratId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('contrat_id'))->equal($contratId));

        return $this->fetchAll($query);
    }

    public function findOneFromContratAndDate($contratId, $annee, $mois)
    {
        $query = $this->getBaseQuery();
        $query
            ->where((new Types\Integer('contrat_id'))->equal($contratId))
            ->where((new Types\Integer('annee'))->equal($annee))
            ->where((new Types\Integer('mois'))->equal($mois));

        return $this->fetchOne($query);
    }

    public function persist(DTO\Bulletin $bulletinDTO)
    {
        if($bulletinDTO->id !== null)
        {
            $bulletin = $this->update($bulletinDTO);
        }
        else
        {
            $bulletin = $this->create($bulletinDTO);
        }

        $lignes = $bulletinDTO->load('lignes');

        foreach($lignes as $ligne)
        {
            $ligne->setBulletinId($bulletinDTO->id);
            $ligne->persist($this->ligneRepository);
        }

        return $bulletin;
    }

    public function create(DTO\Bulletin $bulletinDTO)
    {
        $this->db->insert(
            self::TABLE_NAME,
            array(
                'annee' => $bulletinDTO->annee,
                'mois' => $bulletinDTO->mois,
                'contrat_id' => $bulletinDTO->contratId,
            ),
            array(
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            )
        );

        $bulletinDTO->id = (int) $this->db->lastInsertId();

        return new Domains\Bulletin($bulletinDTO);
    }

    public function update(DTO\Bulletin $bulletinDTO)
    {
         $this->db->update(
            self::TABLE_NAME,
            array(
                'annee' => $bulletinDTO->annee,
                'mois' => $bulletinDTO->mois,
                'contrat_id' => $bulletinDTO->contratId,
            ),
            array(
                'id' => $bulletinDTO->id,
            ),
            array(
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            )
        );

        return new Domains\Bulletin($bulletinDTO);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'mois', 'annee', 'contrat_id')))
            ->from(self::TABLE_NAME)
            ->orderBy('annee', OrderBy::DESC)
            ->orderBy('mois', OrderBy::DESC);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'mois' => new Fields\NotNullable(new Fields\Integer('mois')),
            'annee' => new Fields\NotNullable(new Fields\Integer('annee')),
            'contratId' => new Fields\NotNullable(new Fields\Integer('contrat_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('evenements', function() use($dto) {
            return $this->evenementRepository->findAllFromContrat($dto->contratId, new \DateTime(sprintf('%d-%d', $dto->annee, $dto->mois)), true);
        });

        $dto->set('contrat', function() use($dto) {
            return $this->contratRepository->find($dto->contratId);
        });

        $dto->set('lignes', function() use($dto) {
            return $this->ligneRepository->findFromBulletin($dto->id);
        });

        $dto->set('congesPayes', function() use($dto) {
            return $this->congePayeRepository->findFromContratAndDate($dto->contratId, $dto->annee, $dto->mois);
        });

        return new Domains\Bulletin($dto);
    }

    public function getDTO()
    {
        return new DTO\Bulletin();
    }
}