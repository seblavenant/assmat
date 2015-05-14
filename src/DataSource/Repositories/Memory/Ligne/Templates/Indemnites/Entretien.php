<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Indemnites;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Entretien extends AbstractIndemnite
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Indemnites d\'entretien';
        $ligneDTO->type = Constants\Lignes\Type::INDEMNITES_ENTRETIEN;
        $ligneDTO->action = Constants\Lignes\Action::GAIN;
        $ligneDTO->context = Constants\Lignes\Context::INDEMNITE;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $this->compute($bulletin, $ligneDTO);
        };

        return new Domains\Ligne($ligneDTO);
    }
}