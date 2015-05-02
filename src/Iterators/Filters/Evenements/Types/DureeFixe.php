<?php

namespace Assmat\Iterators\Filters\Evenements\Types;

class DureeFixe extends \FilterIterator
{
    public function accept()
    {
        $evenementType = $this->getInnerIterator()->current();

        return $evenementType->isDureeFixe() === true;
    }
}
