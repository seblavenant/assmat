<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Remunerations;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class IndemnitesCP extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();

        $this->ligneDTO->label = 'Indemnites Congés Payés';
        $this->ligneDTO->typeId = Constants\Lignes\Type::INDEMNITES_CONGES_PAYES;
        $this->ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $this->ligneDTO->contextId = Constants\Lignes\Context::REMUNERATION;
    }
}
