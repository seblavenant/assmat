<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\CongesPayes;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class CpAcquis extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'Congés payés aquis';
        $this->ligneDTO->typeId = Constants\Lignes\Type::CONGES_PAYES_ACQUIS;
        $this->ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $this->ligneDTO->contextId = Constants\Lignes\Context::CONGE_PAYE;
        $this->ligneDTO->quantite = 1;
        
        $this->ligneDTO->baseEditable = true;
    }
}