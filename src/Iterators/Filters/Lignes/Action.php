<?php

namespace Assmat\Iterators\Filters\Lignes;

class Action extends \FilterIterator
{
    private
        $actionId;

    public function __construct(\Iterator $iterator, $actionId)
    {
        parent::__construct($iterator);

        $this->actionId = $actionId;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if($ligne->getActionId() === $this->actionId)
        {
            return true;
        }
    }
}