<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\CongesPayes;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class CpPris extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'Congés payés pris';
        $this->ligneDTO->typeId = Constants\Lignes\Type::CONGES_PAYES_PRIS;
        $this->ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $this->ligneDTO->contextId = Constants\Lignes\Context::CONGE_PAYE;
        $this->ligneDTO->base = 1;
        
        $this->ligneDTO->quantiteEditable = true;
    }
}