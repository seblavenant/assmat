<?php

namespace Assmat\Services\Bulletin\Indemnites;

require_once(__DIR__ . '/../BuilderHelper.php');
require_once(__DIR__ . '/../BuilderValidator.php');

use Assmat\Services\Bulletin\BuilderValidator;
use Assmat\Services\Bulletin\BuilderHelper;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Services\Bulletin;

class SalairesTest extends \PHPUnit_Framework_TestCase
{
    public function testSalaireBuildProvider()
    {
        return array(
            'garde (heures)' => array(
                Constants\Contrats\Salaire::HEURES, 8.5, 85, 65.26,
                array(
                    (new BuilderHelper())->getEvenementGarde()
                ),
            ),
            'garde (mensuel)' => array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300, 998.27,
                array(
                    (new BuilderHelper())->getEvenementGarde()
                ),
            ),
            'absence non payée (heures)' => array(
                Constants\Contrats\Salaire::HEURES, 0, 0, 0,
                array(
                    (new BuilderHelper())->getEvenementAbsenceNonPayee()
                ),
            ),
            'absence non payée (mensuel)' => array(
                Constants\Contrats\Salaire::MENSUALISE, 122.5, 1225, 940.67,
                array(
                    (new BuilderHelper())->getEvenementAbsenceNonPayee(),
                    (new BuilderHelper())->getEvenementGarde()
                ),
            ),
            'absence payée (heures)' => array(
                Constants\Contrats\Salaire::HEURES, 7.5, 75, 57.58,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
            ),
            'absence payée (mensuel)' => array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300, 998.27,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
            ),
            'congé payé (heures)' => array(
                Constants\Contrats\Salaire::HEURES, 7.5, 75, 57.58,
                array(
                    (new BuilderHelper())->getEvenementCongePaye()
                ),
            ),
            'congé payés (mensuel)' => array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300, 998.27,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
            ),
            'jour férié (heures)' => array(
                Constants\Contrats\Salaire::HEURES, 7.5, 75, 57.58,
                array(
                    (new BuilderHelper())->getEvenementJourFerie()
                ),
            ),
            'jour férié (mensuel)' => array(
                Constants\Contrats\Salaire::MENSUALISE, 130, 1300, 998.27,
                array(
                    (new BuilderHelper())->getEvenementJourFerie()
                ),
            ),
        );
    }

    /**
     * @dataProvider testSalaireBuildProvider
     */
    public function testSalaireBuild($typeID, $heures, $salaireBrut, $salaireNet, $evenements)
    {
        $contratDTO = (new BuilderHelper())->getBaseContratDTO();
        $contratDTO->typeId = $typeID;
        $contrat = new Domains\Contrat($contratDTO);

        $bulletinBuilder = new Bulletin\Builder(new Repositories\Memory\Ligne\Template());
        $bulletin = $bulletinBuilder->build($contrat, $evenements, 2015, 01);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertSalaire($heures, $salaireBrut, $salaireNet);
    }
}
