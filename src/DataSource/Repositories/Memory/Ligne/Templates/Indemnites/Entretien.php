<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Indemnites;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class Entretien extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();
        
        $this->ligneDTO->label = 'Indemnites d\'entretien';
        $this->ligneDTO->typeId = Constants\Lignes\Type::INDEMNITES_ENTRETIEN;
        $this->ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $this->ligneDTO->contextId = Constants\Lignes\Context::INDEMNITE;
        
        $this->ligneDTO->baseEditable = true;
        $this->ligneDTO->quantiteEditable = true;
    }
}