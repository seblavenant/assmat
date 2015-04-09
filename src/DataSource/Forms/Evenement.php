<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

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
            ->add('type')
            ->add('contratId')
            ;
    }

    public function getName()
    {
        return 'evenement';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}