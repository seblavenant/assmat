<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\Iterators\Filters as FilterIterators;
use Assmat\DataSource\Repositories;

class Bulletin
{
    private
        $fields,
        $heuresPayees,
        $heuresPayeesParSemaine,
        $heuresNonPayees,
        $joursGardes,
        $congesPayes,
        $salaireBrut,
        $salaireNet,
        $cotisationMontant,
        $indemnitesMontant;

    public function __construct(DTO\Bulletin $bulletinDTO)
    {
        $this->fields = $bulletinDTO;

        $this->heuresPayees = 0;
        $this->heuresPayeesParSemaine = array();
        $this->heuresNonPayees = 0;
        $this->joursGardes = 0;
        $this->congesPayes = 0;
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

    public function getCongesPayes()
    {
        return $this->fields->load('congesPayes');
    }

    public function getSalaireBrut()
    {
        if($this->salaireBrut === null)
        {
            $this->salaireBrut = $this->computeContextMontant(array(Constants\Lignes\Context::REMUNERATION));
        }

        return $this->salaireBrut;
    }

    public function getSalaireNet()
    {
        if($this->salaireNet === null)
        {
            $this->salaireNet = $this->getSalaireBrut() - $this->getCotisationsMontant() + $this->getIndemnitesMontant();
        }

        return $this->salaireNet;
    }

    public function getCotisationsMontant()
    {
        if($this->cotisationMontant === null)
        {
            $this->cotisationMontant = $this->computeContextMontant(array(Constants\Lignes\Context::COTISATION));
        }

        return $this->cotisationMontant;
    }

    public function getIndemnitesMontant()
    {
        if($this->indemnitesMontant === null)
        {
            $this->indemnitesMontant = $this->computeContextMontant(array(Constants\Lignes\Context::INDEMNITE));
        }

        return $this->indemnitesMontant;
    }

    private function computeContextMontant(array $context)
    {
        $salaireBrut = 0;
        $lignesRemuneration = new FilterIterators\Lignes\Context(new \ArrayIterator($this->getLignes()), $context);
        foreach($lignesRemuneration as $ligne)
        {
            $salaireBrut += $ligne->getValeur();
        }

        return $salaireBrut;
    }

    public function addHeuresPayees($heuresPayees, $evenement)
    {
        $this->addHeuresPayeesParSemaine($heuresPayees, $evenement);

        if((int) $evenement->getDate()->format('n') !== (int) $this->getMois())
        {
            return;
        }

        $this->heuresPayees += (float) $heuresPayees;
    }

    public function addHeuresPayeesParSemaine($heuresPayees, $evenement)
    {
        $lastDayOfWeek = new \DateTime(date('Y-m-d', strtotime($evenement->getDate()->format('Y-m-d') . ' sunday this week')));

        if((int) $lastDayOfWeek->format('n') !== (int) $this->getMois())
        {
            return;
        }

        $week = $evenement->getDate()->format('W');
        if(! isset($this->heuresPayeesParSemaine[$week]))
        {
            $this->heuresPayeesParSemaine[$week] = 0;
        }

        $this->heuresPayeesParSemaine[$week] += (float) $heuresPayees;
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
        $this->congesPayes += (int) $congePaye;
    }

    public function getHeuresPayees()
    {
        return $this->heuresPayees;
    }

    public function getHeuresPayeesParSemaine()
    {
        return $this->heuresPayeesParSemaine;
    }

    public function getHeuresComplementaires()
    {
        $heuresComplementaires = 0;
        foreach($this->heuresPayeesParSemaine as $week => $heures)
        {
            $heuresComplementaires += max(0, $heures - $this->getContrat()->getHeuresHebdo());
        }

        return $heuresComplementaires;
    }

    public function getHeuresNonPayees()
    {
        return $this->heuresNonPayees;
    }

    public function getJoursGardes()
    {
        return $this->joursGardes;
    }

    public function persist(Repositories\Bulletin $bulletinRepository)
    {
        return $bulletinRepository->persist($this->fields);
    }
}