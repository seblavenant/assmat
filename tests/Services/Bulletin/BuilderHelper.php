<?php

namespace Assmat\Services\Bulletin;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\Services\Lignes;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Services\Bulletin;

class BuilderHelper
{
    public function getBaseContratDTO()
    {
        $contratDTO = new DTO\Contrat();
        $contratDTO->salaireHoraire = 10;
        $contratDTO->heuresHebdo = 30;
        $contratDTO->joursGarde = 4;

        return $contratDTO;
    }

    private function getBaseEvenementDTO(Domains\EvenementType $evenementType)
    {
        $evenementDTO = new DTO\Evenement();
        $evenementDTO->date = new \DateTime('2015-01-01');
        $evenementDTO->set('type', function() use($evenementType) {
            return $evenementType;
        });

        return $evenementDTO;
    }

    public function getEvenementGarde()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\Garde())->getDomain());
        $evenementGarde->heureDebut = new \DateTime('2015-01-01 08:00');
        $evenementGarde->heureFin = new \DateTime('2015-01-01 16:30');

        return new Domains\Evenement($evenementGarde);
    }

    public function getEvenementAbsenceNonPayee()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\AbsenceNonPayee())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    public function getEvenementAbsencePayee()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\AbsencePayee())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    public function getEvenementCongePaye()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\CongePaye())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    public function getEvenementJourFerie()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\JourFerie())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    public function getIndemnite()
    {
        $indemniteDTO = new DTO\Indemnite();
        $indemniteDTO->contratId = 1;
        $indemniteDTO->typeId = Constants\Lignes\Type::INDEMNITES_NOURRITURE;
        $indemniteDTO->montant = 2.5;

        return new Domains\Indemnite($indemniteDTO);
    }
}