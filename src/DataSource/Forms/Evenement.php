<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Evenement extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                'input' => 'string',
                'widget' => 'single_text',
            ))
            ->add('heureDebut', 'time', array(
                'input' => 'string',
                'widget' => 'single_text',
            ))
            ->add('heureFin', 'time', array(
                'input' => 'string',
                'widget' => 'single_text',
            ))
            ->add('typeId')
            ->add('contratId')
            ;
    }

    public function getName()
    {
        return 'evenements';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}