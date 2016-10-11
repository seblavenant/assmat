<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Cotisations;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class SecuriteSociale extends AbstractTemplate
{
    const
        TAUX = 7.9;

    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'Sécurité sociale';
        $this->ligneDTO->typeId = Constants\Lignes\Type::SECURITE_SOCIALE;
        $this->ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $this->ligneDTO->contextId = Constants\Lignes\Context::COTISATION;
        $this->ligneDTO->quantite = self::TAUX / 100;
        
        $this->ligneDTO->quantiteEditable = true;
        $this->ligneDTO->taux = true;
    }
}