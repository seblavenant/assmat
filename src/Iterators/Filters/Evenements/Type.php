<?php

namespace Assmat\Iterators\Filters\Evenements;

class Type extends \FilterIterator
{
    private
        $typesId;

    public function __construct(\Iterator $iterator, array $typesId)
    {
        parent::__construct($iterator);

        $this->typesId = $typesId;
    }

    public function accept()
    {
        $evenement = $this->getInnerIterator()->current();

        if(in_array($evenement->getTypeId(), $this->typesId))
        {
            return true;
        }
    }
}