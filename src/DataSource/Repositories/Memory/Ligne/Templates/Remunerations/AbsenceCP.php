<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Remunerations;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class AbsenceCP extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();

        $this->ligneDTO->label = 'Absence Congés Payés';
        $this->ligneDTO->typeId = Constants\Lignes\Type::ABSENCE_CONGES_PAYES;
        $this->ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $this->ligneDTO->contextId = Constants\Lignes\Context::REMUNERATION;
    }
}
