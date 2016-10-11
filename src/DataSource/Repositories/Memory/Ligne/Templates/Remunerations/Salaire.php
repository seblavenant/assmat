<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Remunerations;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class Salaire extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'Salaire';
        $this->ligneDTO->typeId = Constants\Lignes\Type::SALAIRE;
        $this->ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $this->ligneDTO->contextId = Constants\Lignes\Context::REMUNERATION;
        
        $this->ligneDTO->baseEditable = true;
        $this->ligneDTO->quantiteEditable = true;
    }
}