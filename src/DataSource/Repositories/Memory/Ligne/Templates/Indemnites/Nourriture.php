<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Indemnites;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories\Memory\Ligne\Templates\AbstractTemplate;

class Nourriture extends AbstractTemplate
{
    public function __construct()
    {
        parent::__construct();

        $this->ligneDTO->label = 'Indemnites nourriture';
        $this->ligneDTO->typeId = Constants\Lignes\Type::INDEMNITES_NOURRITURE;
        $this->ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $this->ligneDTO->contextId = Constants\Lignes\Context::INDEMNITE;

        $this->ligneDTO->quantiteEditable = true;
        $this->ligneDTO->baseEditable = true;
    }
}
