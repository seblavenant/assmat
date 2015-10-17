<?php
namespace Assmat\Services\Form;

use Silex\Translator;

class Errors
{
    private
        $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    public function getMessages(\Symfony\Component\Form\Form $form)
    {
        return $this->getErrors($form);
    }

    private function getErrors(\Symfony\Component\Form\Form $form, $parentForm = array())
    {
        $parentForm[] = $form->getName();
        $errors = array();
        foreach ($form->getErrors() as $error)
        {
            return array(
                'label' => addSlashes($this->translator->trans(implode('.', $parentForm))),
                'error' => $error->getMessage()
            );
        }

        foreach ($form->all() as $fieldName => $child)
        {
            if ($err = $this->getErrors($child, $parentForm))
            {
                $errors[implode('_', $parentForm).'_'.$fieldName] = $err;
            }
        }

        return $errors;
    }
}