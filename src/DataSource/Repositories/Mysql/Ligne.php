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

    private
        $ligneTemplateRepository;

    public function __construct(Mysql $db, Repositories\LigneTemplate $ligneTemplateRepository)
    {
        parent::__construct($db);
        
        $this->ligneTemplateRepository = $ligneTemplateRepository;
    }
        
    public function findFromBulletin($bulletinId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('bulletin_id'))->equal($bulletinId));

        return $this->fetchAll($query);
    }

    public function countAllFromContratAndContext($contratId, $contextId, \DateTime $dateEnd, \DateTime $dateStart = null)
    {
        $fields = $this->prefixTableFields(array('type_id'));
        array_push($fields, 'sum( valeur ) AS count');

        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($fields)
            ->from(self::TABLE_NAME)
            ->leftJoin(Repositories\Mysql\Bulletin::TABLE_NAME)->on($this->prefixTableField('bulletin_id'), 'bulletin.id')
            ->where((new Types\Integer('context_id'))->equal($contextId))
            ->where((new Types\Integer('contrat_id'))->equal($contratId))
            ->where((new Types\String('concat( bulletin.annee, LPAD( bulletin.mois, 2, "0" ))'))->lowerOrEqualThan($dateEnd->format('Ym')))
            ->groupBy('type_id')
            ;
        
        if($dateStart !== null)
        {
            $query->where((new Types\String('concat( bulletin.annee, LPAD( bulletin.mois, 2, "0" ))'))->greaterOrEqualThan($dateStart->format('Ym')));
        }

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
            ->select($this->prefixTableFields(array('id', 'label', 'type_id', 'action_id', 'context_id', 'base', 'quantite', 'valeur', 'bulletin_id')))
            ->from(self::TABLE_NAME);

        return $query;
    }
    
    protected function buildDTOObject($record)
    {
        $dto = parent::buildDTOObject($record);
        
        $ligneTemplate = $this->ligneTemplateRepository->find($dto->typeId);

        if(! $ligneTemplate instanceof Domains\Ligne)
        {
            throw new \RuntimeException(sprintf('ligneTemplate "%s" not found', $dto->typeId));
        }
        
        $dto->baseEditable = $ligneTemplate->isBaseEditable();
        $dto->quantiteEditable = $ligneTemplate->isQuantiteEditable();
        $dto->taux = $ligneTemplate->isTaux();

        return $dto;
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
    
    public function update(DTO\Ligne $ligneDTO)
    {
        $this->db->update(
            self::TABLE_NAME,
            array(
                'label' => $ligneDTO->label,
                'type_id' => $ligneDTO->typeId,
                'action_id' => $ligneDTO->actionId,
                'context_id' => $ligneDTO->contextId,
                'base' => $ligneDTO->base,
                'quantite' => $ligneDTO->quantite,
                'valeur' => $ligneDTO->valeur,
                'bulletin_id' => $ligneDTO->bulletinId,
            ),
            array(
                'id' => $ligneDTO->id,
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
                \PDO::PARAM_INT,
            )
        );

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