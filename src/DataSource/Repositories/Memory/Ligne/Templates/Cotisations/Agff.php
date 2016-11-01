<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Cotisations;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class Agff extends AbstractTemplate
{
    const
        TAUX = 0.80;

    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'AGFF';
        $this->ligneDTO->typeId = Constants\Lignes\Type::AGFF;
        $this->ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $this->ligneDTO->contextId = Constants\Lignes\Context::COTISATION;
        $this->ligneDTO->quantite = self::TAUX / 100;
        
        $this->ligneDTO->taux = true;
        $this->ligneDTO->quantiteEditable = true;
    }
}
