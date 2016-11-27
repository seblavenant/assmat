<?php

namespace Assmat\DataSource\Constants\Lignes;

interface Type
{
    const
        SALAIRE = 100,
        HEURES_COMPLEMENTAIRES = 101,
        SALAIRE_NET = 199,

        CSG_RDS = 200,
        CSG_DEDUCTIBLE = 201,
        SECURITE_SOCIALE = 202,
        RETRAITE_COMPLEMENTAIRE = 203,
        PREVOYANCE = 204,
        AGFF = 205,
        ASSURANCE_CHOMAGE = 206,

        INDEMNITES_NOURRITURE = 300,
        INDEMNITES_ENTRETIEN = 301,

        CONGES_PAYES_ACQUIS = 400,
        CONGES_PAYES_PRIS = 401,
        CONGES_PAYES_ACQUIS_REFERENCE = 402,
        CONGES_PAYES_PRIS_REFERENCE = 403,
        CONGES_PAYES_RESTANT = 410;
}