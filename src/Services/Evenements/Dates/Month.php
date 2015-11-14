<?php

namespace Assmat\Services\Evenements\Dates;

class Month implements Date
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

    public function getDate()
    {
        return $this->date->format('Y-m');
    }
}