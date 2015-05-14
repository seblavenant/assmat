<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Indemnites;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Nourriture
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Indemnites nourriture';
        $ligneDTO->type = Constants\Lignes\Type::INDEMNITES_NOURRITURE;
        $ligneDTO->action = Constants\Lignes\Action::GAIN;
        $ligneDTO->context = Constants\Lignes\Context::INDEMNITE;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $contrat = $bulletin->getContrat();
            $joursGardes = $bulletin->getJoursGardes();
            $indemnites = $contrat->getIndemnites();
            if(empty($indemnites))
            {
                return;
            }
            $indemniteNourriture = $this->getIndemniteByTypeId($indemnites, $ligneDTO->type);
            $montant = $indemniteNourriture->getMontant();
            $ligneDTO->base = $montant;
            $ligneDTO->quantite = $joursGardes;
            $ligneDTO->valeur = $joursGardes * $montant;
        };

        return new Domains\Ligne($ligneDTO);
    }

    private function getIndemniteByTypeId(array $indemnites, $typeId)
    {
        foreach($indemnites as $indemnite)
        {
            if($indemnite->getTypeId() === $typeId)
            {
                return $indemnite;
            }
        }

        throw new \Exception('No indemnite found for ' . $typeId);
    }
}