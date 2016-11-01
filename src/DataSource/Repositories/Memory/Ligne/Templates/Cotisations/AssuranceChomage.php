<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Cotisations;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class AssuranceChomage extends AbstractTemplate
{
    const
        TAUX = 2.40;

    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'Assurance chômage';
        $this->ligneDTO->typeId = Constants\Lignes\Type::ASSURANCE_CHOMAGE;
        $this->ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $this->ligneDTO->contextId = Constants\Lignes\Context::COTISATION;
        $this->ligneDTO->quantite = self::TAUX / 100;

        $this->ligneDTO->quantiteEditable = true;
        $this->ligneDTO->taux = true;
    }
}
