<?php

namespace Assmat\Services\Providers;

use Assmat\Services\Lignes\Builders;
use Assmat\DataSource\Constants;

class LigneBuilder implements ServiceProvider
{
    private
        $builders;
    
    public function __construct()
    {
        $this->builders = [
            Constants\Lignes\Type::SALAIRE => function() {
                return new Builders\Remunerations\Salaire();
            },
            Constants\Lignes\Type::HEURES_COMPLEMENTAIRES => function() {
                return new Builders\Remunerations\HeuresComplementaires();
            },
            Constants\Lignes\Type::CSG_RDS => function() {
                return new Builders\Cotisations\CsgRds();
            },
            Constants\Lignes\Type::CSG_DEDUCTIBLE => function() {
                return new Builders\Cotisations\CsgDeductible();
            },
            Constants\Lignes\Type::SECURITE_SOCIALE => function() {
                return new Builders\Cotisations\SecuriteSociale();
            },
            Constants\Lignes\Type::RETRAITE_COMPLEMENTAIRE => function() {
                return new Builders\Cotisations\RetraiteComplementaire();
            },
            Constants\Lignes\Type::PREVOYANCE => function() {
                return new Builders\Cotisations\Prevoyance();
            },
            Constants\Lignes\Type::AGFF => function() {
                return new Builders\Cotisations\Agff();
            },
            Constants\Lignes\Type::ASSURANCE_CHOMAGE => function() {
                return new Builders\Cotisations\AssuranceChomage();
            },
            Constants\Lignes\Type::INDEMNITES_NOURRITURE => function() {
                return new Builders\Indemnites\Nourriture();
            },
            Constants\Lignes\Type::INDEMNITES_ENTRETIEN => function() {
                return new Builders\Indemnites\Entretien();
            },
            Constants\Lignes\Type::CONGES_PAYES_ACQUIS => function() {
                return new Builders\CongesPayes\CpAcquis();
            },
            Constants\Lignes\Type::CONGES_PAYES_PRIS => function() {
                return new Builders\CongesPayes\CpPris();
            },
        ];
    }
    
    public function get($serviceName)
    {
        if(empty($this->builders[$serviceName]))
        {
            throw new \RuntimeException(sprintf('Service "%s" not found', $serviceName));
        }
        
        return $this->builders[$serviceName];
    }
}
