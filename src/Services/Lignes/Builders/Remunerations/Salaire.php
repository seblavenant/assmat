<?php

namespace Assmat\Services\Lignes\Builders\Remunerations;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Constants;

class Salaire
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        if(empty($ligne->getBase()))
        {
            $ligne->setBase($bulletin->getContrat()->getSalaireHoraire());
        }

        if(empty($ligne->getQuantite()))
        {
            $ligne->setQuantite($this->computeQuantite($bulletin));
        }

        $ligne->computeValeur();
    }
    
    private function computeQuantite(Domains\Bulletin $bulletin)
    {
        $contrat = $bulletin->getContrat();
    
        switch($contrat->getTypeId())
        {
            case Constants\Contrats\Salaire::MENSUALISE:
                $quantite = $contrat->getHeuresMensuel();
                $quantite -= $bulletin->getHeuresNonPayees();
                break;
            case Constants\Contrats\Salaire::HEURES:
            default:
                $quantite = $bulletin->getHeuresPayees();
                break;
        }

        return $quantite;
    }
}
