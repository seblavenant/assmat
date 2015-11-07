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

class Ligne extends AbstractMysql implements Repositories\Ligne
{
    const
        TABLE_NAME = 'ligne';

    public function findFromBulletin($bulletinId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('bulletin_id'))->equal($bulletinId));

        return $this->fetchAll($query);
    }

    public function countAllFromContratAndContext($contratId, $contextId, \DateTime $date)
    {
        $fields = $this->prefixTableFields(array('type_id'));
        array_push($fields, 'sum( valeur ) AS count');

        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($fields)
            ->from(self::TABLE_NAME)
            ->leftJoin(Repositories\Mysql\Bulletin::TABLE_NAME)->on($this->prefixTableField('bulletin_id'), 'bulletin.id')
            ->where((new Types\Integer('context_id'))->equal($contextId))
            ->where((new Types\Integer('contrat_id'))->equal($contratId))
            ->where((new Types\String('concat( bulletin.annee, LPAD( bulletin.mois, 2, "0" ))'))->lowerOrEqualThan($date->format('Ym')))
            ->groupBy('type_id')
            ;

        $dataSet = $this->db->fetchAll($query->toString());

        $contexts = array();
        if(!empty($dataSet))
        {
            foreach($dataSet as $contextLigne)
            {
                $contexts[$contextLigne['type_id']] = $contextLigne['count'];
            }
        }

        return $contexts;
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'label', 'type_id', 'action_id', 'context_id', 'base', 'taux', 'quantite', 'valeur', 'bulletin_id')))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function create(DTO\Ligne $ligneDTO)
    {
        $this->db->insert(
            self::TABLE_NAME,
            array(
                'label' => $ligneDTO->label,
                'type_id' => $ligneDTO->typeId,
                'action_id' => $ligneDTO->actionId,
                'context_id' => $ligneDTO->contextId,
                'base' => $ligneDTO->base,
                'taux' => $ligneDTO->taux,
                'quantite' => $ligneDTO->quantite,
                'valeur' => $ligneDTO->valeur,
                'bulletin_id' => $ligneDTO->bulletinId,
            ),
            array(
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
            )
        );

        $ligneDTO->id = $this->db->lastInsertId();

        return new Domains\Ligne($ligneDTO);
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\Integer('id')),
            'label' => new Fields\NotNullable(new Fields\String('label')),
            'typeId' => new Fields\NotNullable(new Fields\Integer('type_id')),
            'actionId' => new Fields\NotNullable(new Fields\Integer('action_id')),
            'contextId' => new Fields\NotNullable(new Fields\Integer('context_id')),
            'base' => new Fields\Float('base'),
            'taux' => new Fields\Float('taux'),
            'quantite' => new Fields\Float('quantite'),
            'valeur' => new Fields\Float('valeur'),
            'bulletinId' => new Fields\NotNullable(new Fields\Integer('bulletin_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        return new Domains\Ligne($dto);
    }

    public function getDTO()
    {
        return new DTO\Ligne();
    }
}