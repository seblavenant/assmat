<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\Iterators\Filters as FilterIterators;

class Bulletin
{
    private
        $fields,
        $heuresPayees,
        $heuresNonPayees,
        $joursGardes,
        $congesPayes,
        $salaireBrut,
        $salaireNet;

    public function __construct(DTO\Bulletin $bulletinDTO)
    {
        $this->fields = $bulletinDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getAnnee()
    {
        return $this->fields->annee;
    }

    public function getMois()
    {
        return $this->fields->mois;
    }

    public function getLignes()
    {
        return $this->fields->load('lignes');
    }

    public function getContrat()
    {
        return $this->fields->load('contrat');
    }

    public function getEvenements()
    {
        return $this->fields->load('evenements');
    }

    public function getSalaireBrut()
    {
        if($this->salaireBrut === null)
        {
            $salaireBrut = 0;
            $lignesRemuneration = new FilterIterators\Lignes\Type(new \ArrayIterator($this->getLignes()), array(Constants\Lignes\Type::SALAIRE));
            foreach($lignesRemuneration as $ligne)
            {
                $salaireBrut += $ligne->getValeur();
            }

            $this->salaireBrut = $salaireBrut;
        }

        return $this->salaireBrut;
    }

    public function getSalaireNet()
    {
        if($this->salaireNet === null)
        {
            $salaireNet = 0;
            foreach($this->getLignes() as $ligne)
            {
                $valeur = $ligne->getValeur();
                if($ligne->getAction() === Constants\Lignes\Action::RETENUE)
                {
                    $valeur *= -1;
                }

                $salaireNet += $valeur;
            }

            $this->salaireNet = $salaireNet;
        }

        return $this->salaireNet;
    }

    public function addHeuresPayees($heuresPayees)
    {
        $this->heuresPayees += (float) $heuresPayees;
    }

    public function addHeuresNonPayees($heuresNonPayees)
    {
        $this->heuresNonPayees += (float) $heuresNonPayees;
    }

    public function addJourGarde($jourGarde)
    {
        $this->joursGardes += (int) $jourGarde;
    }

    public function addCongePaye($congePaye)
    {
        $this->congePaye += (int) $congePaye;
    }

    public function getHeuresPayee()
    {
        return $this->heuresPayees;
    }

    public function getHeuresNonPayee()
    {
        return $this->heuresNonPayees;
    }
}