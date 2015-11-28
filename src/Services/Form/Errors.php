<?php
namespace Assmat\Services\Form;

use Silex\Translator;
use Symfony\Component\Form\FormInterface;

class Errors
{
    private
        $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getMessages(FormInterface $form)
    {
        return $this->getErrors($form);
    }

    private function getErrors(FormInterface $form, $parentForm = array())
    {
        $formName = $form->getName();
        if(!empty($formName))
        {
            $parentForm[] = $form->getName();
        }
        $errors = array();
        foreach($form->getErrors() as $error)
        {
            return array('form' => array(
                'label' => addSlashes($this->translator->trans(implode('.', $parentForm))),
                'error' => $error->getMessage()
            ));
        }

        foreach($form->all() as $fieldName => $child)
        {
            if($err = $this->getErrors($child, $parentForm))
            {
                $fieldPrefix = implode('_', $parentForm);
                if(!empty($fieldPrefix))
                {
                    $fieldName = $fieldPrefix . '_' . $fieldName;
                }

                $errors[$fieldName] = $err;
            }
        }

        return $errors;
    }
}