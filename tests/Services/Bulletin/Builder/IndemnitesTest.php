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

class IndemnitesTest extends \PHPUnit_Framework_TestCase
{
    public function testIndemnitesProvider()
    {
        return array(
            'garde' => array(
                array(1, 2.5),
                array(
                    (new BuilderHelper())->getEvenementGarde()
                ),
            ),
            'congés payés' => array(
                array(0, 0),
                array(
                    (new BuilderHelper())->getEvenementCongePaye()
                ),
            ),
            'absence payée' => array(
                array(0, 0),
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
            ),
            'absence non payée' => array(
                array(0, 0),
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
            ),
            'jour férié' => array(
                array(0, 0),
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
            ),
        );
    }

    /**
     * @dataProvider testIndemnitesProvider
     */
    public function testIndemnitesBuild($expected, $evenements)
    {
        list($quantite, $montant) = $expected;

        $contratDTO = (new BuilderHelper())->getBaseContratDTO();
        $contratDTO->set('indemnites', function() {
            return array(
                (new BuilderHelper())->getIndemnite(),
            );
        });
        $contrat = new Domains\Contrat($contratDTO);

        $bulletinBuilder = new Bulletin\Builder(new Repositories\Memory\Ligne\Template());
        $bulletin = $bulletinBuilder->build($contrat, $evenements, 2015, 01);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertIndemnites(Constants\Lignes\Type::INDEMNITES_NOURRITURE, $quantite, $montant);
    }
}
