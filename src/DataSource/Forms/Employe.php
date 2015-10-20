<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Employe extends AbstractType
{
    private
        $contactForm;

    public function __construct($contactForm)
    {
        $this->contactForm = $contactForm;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ssId', 'text', array(
                'label' => 'employes.ssId',
                'required' => false,
                'constraints' => array(
                     new Constraints\NotBlank(),
                 ),
            ))
            ;

        $this->contactForm->buildForm($builder, $options);
    }

    public function getName()
    {
        return 'employes';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }
}