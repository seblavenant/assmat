<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Constants;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Conditions;
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

    private function findLignesFromContextAndPeriode($contratId, $contextId, \DateTime $dateDebut, \DateTime $dateFin)
    {
        $query = $this->getBaseQuery();
        $query
            ->select('concat( bulletin.annee, LPAD( bulletin.mois, 2, "0" ) ) AS date')
            ->leftJoin(Repositories\Mysql\Bulletin::TABLE_NAME)->on($this->addTableName('bulletin_id'), 'bulletin.id')
            ->where((new Types\Integer('context_id'))->equal($contextId))
            ->where((new Types\Integer('contrat_id'))->equal($contratId))
            ->having((new Types\String('date'))->between($dateDebut->format('Ym'), $dateFin->format('Ym')))
            ;

        return $this->fetchAll($query);
    }

    public function findCongePayesFromContratAndDate($contratId, \DateTime $date)
    {
        $currentYear = (int) $date->format('Y');

        $yearDebut = $currentYear;
        $yearFin = $currentYear;
        if((int) $date->format('m') < 6)
        {
            $yearDebut -= 1;
            $yearFin -= 1;
        }

        $dateDebutPreviousYear = new \DateTime(($yearDebut -1) . '-06-01');
        $dateFinPreviousYear = new \DateTime($yearFin . '-05-31');
        $dateDebutCurrentYear = new \DateTime($yearDebut . '-06-01');
        $dateFinCurrentYear = new \DateTime($currentYear . '-' . $date->format('m') . '-' . $date->format('t'));

        return array(
            'N' => $this->findLignesFromContextAndPeriode($contratId, Constants\Lignes\Context::CONGE_PAYE, $dateDebutCurrentYear, $dateFinCurrentYear),
            'N-1' => $this->findLignesFromContextAndPeriode($contratId, Constants\Lignes\Context::CONGE_PAYE, $dateDebutPreviousYear, $dateFinPreviousYear),
        );
    }

    private function getBaseQuery()
    {
        $fields = array('id', 'label', 'type_id', 'action_id', 'context_id', 'base', 'taux', 'quantite', 'valeur', 'bulletin_id');
        $fieldsNamed = array_map(array($this, 'addTableName'), $fields);

        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($fieldsNamed)
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function addTableName($field)
    {
        return self::TABLE_NAME . '.' . $field;
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