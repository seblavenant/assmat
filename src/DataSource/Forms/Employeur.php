<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Employeur extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pajeEmploiId', 'text', array(
                'label' => 'employeurs.pajeEmploiId',
                'required' => false,
            ))
            ->add('id', 'hidden')
            ;

        return $builder;
    }

    public function getName()
    {
        return 'employeurs';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }
}