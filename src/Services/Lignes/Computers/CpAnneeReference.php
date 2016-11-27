<?php

namespace Assmat\Services\Lignes\Computers;

class CpAnneeReference
{
    public function compute($annee = null, $mois = null)
    {
        if(empty($annee))
        {
            $annee = (new \DateTime())->format('Y');
        }

        if(! in_array((int) $mois, range(1, 12)))
        {
            $mois = (new \DateTime())->format('m');
        }

        $anneeReference = (int) $annee - 1;
        if($mois < 6)
        {
            $anneeReference -= 1;
        }

        return $anneeReference;
    }
}
