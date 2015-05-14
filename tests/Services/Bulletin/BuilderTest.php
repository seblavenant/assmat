<?php

namespace Assmat\Services\Bulletin;

require_once(__DIR__ . '/BuilderValidator.php');

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\Services\Lignes;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Constants\Lignes\Code;
use Assmat\Services\Bulletin;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCotisationsBuild()
    {
        $contrat = new Domains\Contrat($this->getBaseContratDTO());

        $evenements = array(
            $this->getEvenementGarde()
        );

        $bulletinBuilder = new Bulletin\Builder(new Repositories\Memory\Ligne\Template());
        $bulletin = $bulletinBuilder->build($contrat, $evenements);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertCotisation(Constants\Lignes\Type::CSG_RDS, 2.42);
        $builderValidator->assertCotisation(Constants\Lignes\Type::CSG_DEDUCTIBLE, 4.26);
        $builderValidator->assertCotisation(Constants\Lignes\Type::SECURITE_SOCIALE, 6.72);
        $builderValidator->assertCotisation(Constants\Lignes\Type::RETRAITE_COMPLEMENTAIRE, 2.64);
        $builderValidator->assertCotisation(Constants\Lignes\Type::PREVOYANCE, 0.98);
        $builderValidator->assertCotisation(Constants\Lignes\Type::AGFF, 0.68);
        $builderValidator->assertCotisation(Constants\Lignes\Type::ASSURANCE_CHOMAGE, 2.04);
    }

    public function testIndemnitesBuild()
    {
        $contratDTO = $this->getBaseContratDTO();
        $contratDTO->set('indemnites', function(){
            return array(
                $this->getIndemnite(),
            );
        });
        $contrat = new Domains\Contrat($contratDTO);

        $evenements = array(
            $this->getEvenementGarde()
        );

        $bulletinBuilder = new Bulletin\Builder(new Repositories\Memory\Ligne\Template());
        $bulletin = $bulletinBuilder->build($contrat, $evenements);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertIndemnites(Constants\Lignes\Type::INDEMNITES_NOURRITURE, 1, 2.5);
    }

    public function testSalaireBuildProvider()
    {
        return array(
            // test garde
            array(
                Constants\Contrats\Salaire::HEURES, 8.5, 85,
                array(
                    $this->getEvenementGarde()
                ),
            ),
            array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300,
                array(
                    $this->getEvenementGarde()
                ),
            ),
            // test absence non payee
            array(
                Constants\Contrats\Salaire::HEURES, 0, 0,
                array(
                    $this->getEvenementAbsenceNonPayee()
                ),
            ),
            array(
                Constants\Contrats\Salaire::MENSUALISE, 122.5, 1225,
                array(
                    $this->getEvenementAbsenceNonPayee()
                ),
            ),
            // test absence payee
            array(
                Constants\Contrats\Salaire::HEURES, 7.5, 75,
                array(
                    $this->getEvenementAbsencePayee()
                ),
            ),
            array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300,
                array(
                    $this->getEvenementAbsencePayee()
                ),
            ),
            // test congés payés
            array(
                Constants\Contrats\Salaire::HEURES, 7.5, 75,
                array(
                    $this->getEvenementCongePaye()
                ),
            ),
            array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300,
                array(
                    $this->getEvenementAbsencePayee()
                ),
            ),
        );
    }

    /**
     * @dataProvider testSalaireBuildProvider
     */
    public function testSalaireBuild($typeID, $heures, $salaire, $evenements, $skipped = false)
    {
        if($skipped === true)
        {
            $this->markTestIncomplete(
                'This functionality has not been implemented yet.'
            );
        }

        $contratDTO = $this->getBaseContratDTO();
        $contratDTO->typeId = $typeID;
        $contrat = new Domains\Contrat($contratDTO);

        $bulletinBuilder = new Bulletin\Builder(new Repositories\Memory\Ligne\Template());
        $bulletin = $bulletinBuilder->build($contrat, $evenements);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertSalaire($heures, $salaire);
    }

    private function getBaseContratDTO()
    {
        $contratDTO = new DTO\Contrat();
        $contratDTO->salaireHoraire = 10;
        $contratDTO->heuresHebdo = 30;
        $contratDTO->joursGarde = 4;

        return $contratDTO;
    }

    private function getBaseEvenementDTO($evenementType)
    {
        $evenementDTO = new DTO\Evenement();
        $evenementDTO->date = new \DateTime('2015-01-01');
        $evenementDTO->set('type', function() use($evenementType) {
            return $evenementType;
        });

        return $evenementDTO;
    }

    private function getEvenementGarde()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\Garde())->getDomain());
        $evenementGarde->heureDebut = new \DateTime('2015-01-01 08:00');
        $evenementGarde->heureFin = new \DateTime('2015-01-01 16:30');

        return new Domains\Evenement($evenementGarde);
    }

    private function getEvenementAbsenceNonPayee()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\AbsenceNonPayee())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    private function getEvenementAbsencePayee()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\AbsencePayee())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    private function getEvenementCongePaye()
    {
        $evenementGarde = $this->getBaseEvenementDTO((new Repositories\Memory\Evenement\Types\CongePaye())->getDomain());

        return new Domains\Evenement($evenementGarde);
    }

    private function getIndemnite()
    {
        $indemniteDTO = new DTO\Indemnite();
        $indemniteDTO->contratId = 1;
        $indemniteDTO->typeId = Constants\Lignes\Type::INDEMNITES_NOURRITURE;
        $indemniteDTO->montant = 2.5;

        return new Domains\Indemnite($indemniteDTO);
    }
}