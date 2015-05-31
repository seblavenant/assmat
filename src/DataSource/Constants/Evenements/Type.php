<?php

namespace Assmat\DataSource\Constants\Evenements;

interface Type
{
    const
        GARDE = 1,
        CONGE_PAYE = 2,
        ABSENCE_PAYEE = 4,
        ABSENCE_NON_PAYEE = 3,
        JOUR_FERIE = 5;
}