<?php

namespace Assmat\Iterators\Filters\Lignes;

class Context extends \FilterIterator
{
    private
        $contextsId;

    public function __construct(\Iterator $iterator, array $contextsId)
    {
        parent::__construct($iterator);

        $this->contextsId = $contextsId;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if(in_array($ligne->getContextId(), $this->contextsId))
        {
            return true;
        }
    }
}