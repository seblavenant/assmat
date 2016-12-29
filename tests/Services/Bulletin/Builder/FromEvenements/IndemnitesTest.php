<?php

namespace Assmat\Services\Bulletin\Indemnites\FromEvenements;

require_once(__DIR__ . '/../../BuilderHelper.php');
require_once(__DIR__ . '/../../BuilderValidator.php');

use Assmat\Services\Bulletin\BuilderValidator;
use Assmat\Services\Bulletin\BuilderHelper;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Services\Bulletin;
use Assmat\Services\Providers;

class IndemnitesTest extends \PHPUnit_Framework_TestCase
{
    public function testIndemnitesProvider()
    {
        return array(
            'nourriture - garde' => array(
                array(1, 2.5),
                Constants\Lignes\Type::INDEMNITES_NOURRITURE,
                array(
                    (new BuilderHelper())->getEvenementGarde()
                ),
                array(
                    (new BuilderHelper())->getIndemniteNourriture()
                )
            ),
            'entretien - garde' => array(
                array(1, 3.2),
                Constants\Lignes\Type::INDEMNITES_ENTRETIEN,
                array(
                    (new BuilderHelper())->getEvenementGarde()
                ),
                array(
                    (new BuilderHelper())->getIndemniteEntretien()
                )
            ),
            'nourriture - congés payés' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_NOURRITURE,
                array(
                    (new BuilderHelper())->getEvenementCongePaye()
                ),
                array(
                    (new BuilderHelper())->getIndemniteNourriture()
                )
            ),
            'entretien - congés payés' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_ENTRETIEN,
                array(
                    (new BuilderHelper())->getEvenementCongePaye()
                ),
                array(
                    (new BuilderHelper())->getIndemniteEntretien()
                )
            ),
            'nourriture - absence payée' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_NOURRITURE,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
                array(
                    (new BuilderHelper())->getIndemniteNourriture()
                )
            ),
            'entretien - absence payée' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_ENTRETIEN,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
                array(
                    (new BuilderHelper())->getIndemniteEntretien()
                )
            ),
            'nourriture - absence non payée' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_NOURRITURE,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
                array(
                    (new BuilderHelper())->getIndemniteNourriture()
                )
            ),
            'entretien - absence non payée' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_ENTRETIEN,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
                array(
                    (new BuilderHelper())->getIndemniteEntretien()
                )
            ),
            'nourriture - jour férié' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_NOURRITURE,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
                array(
                    (new BuilderHelper())->getIndemniteNourriture()
                )
            ),
            'entretien - jour férié' => array(
                array(0, 0),
                Constants\Lignes\Type::INDEMNITES_ENTRETIEN,
                array(
                    (new BuilderHelper())->getEvenementAbsencePayee()
                ),
                array(
                    (new BuilderHelper())->getIndemniteEntretien()
                )
            ),
        );
    }

    /**
     * @dataProvider testIndemnitesProvider
     */
    public function testIndemnitesBuild($expected, $typeId, $evenements, $indemnites)
    {
        list($quantite, $montant) = $expected;

        $contratDTO = (new BuilderHelper())->getBaseContratDTO();
        $contratDTO->set('indemnites', function() use($indemnites) {
            return $indemnites;
        });
        $contrat = new Domains\Contrat($contratDTO);

        $bulletinBuilder = new Bulletin\Builders\FromEvenements(new Repositories\Memory\Ligne\Template(), new Repositories\Memory\CongePaye(), new Providers\LigneBuilder());
        $bulletin = $bulletinBuilder->build($contrat, $evenements, 2015, 01);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertIndemnites($typeId, $quantite, $montant);
    }
}
