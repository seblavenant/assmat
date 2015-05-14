<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Indemnites;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;

abstract class AbstractIndemnite
{
    public function compute(Domains\Bulletin $bulletin, DTO\Ligne $ligneDTO)
    {
        $contrat = $bulletin->getContrat();
        $joursGardes = $bulletin->getJoursGardes();
        $indemnites = $contrat->getIndemnites();
        if(empty($indemnites))
        {
            return;
        }
        $indemnite = $this->getIndemniteByTypeId($indemnites, $ligneDTO->type);
        if(empty($indemnite))
        {
            return;
        }
        $montant = $indemnite->getMontant();
        $ligneDTO->base = $montant;
        $ligneDTO->quantite = $joursGardes;
        $ligneDTO->valeur = $joursGardes * $montant;
    }

    public function getIndemniteByTypeId(array $indemnites, $typeId)
    {
        foreach($indemnites as $indemnite)
        {
            if($indemnite->getTypeId() === $typeId)
            {
                return $indemnite;
            }
        }
    }
}