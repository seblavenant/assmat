<?php

namespace Assmat\Services\Evenements\Periods;

class Day implements Period
{
    private
        $date;

    public function __construct(\DateTime $date = null)
    {
        if($date === null)
        {
            $date = new \DateTime();
        }

        $this->date = $date;
    }

    public function getPeriod()
    {
        return $this->date->format('Y-m-d');
    }
}
