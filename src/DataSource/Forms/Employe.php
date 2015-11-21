<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Employe extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ssId', 'text', array(
                'label' => 'employes.ssId',
                'required' => false,
            ))
            ->add('id', 'hidden')
            ;

        return $builder;
    }

    public function getName()
    {
        return 'employes';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }
}