<?php

namespace Assmat\Iterators\Filters\Lignes;

class Code extends \FilterIterator
{
    private
        $codes;

    public function __construct(\Iterator $iterator, array $codes)
    {
        parent::__construct($iterator);

        $this->codes = $codes;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if(in_array($ligne->getCode(), $this->codes))
        {
            return true;
        }
    }
}
