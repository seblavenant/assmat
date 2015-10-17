<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Contrat extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'label' => 'contrats.nom',
                'required' => true,
                'constraints' => array(
                     new Constraints\NotBlank(),
                     new Constraints\Length(array('min' => 5)),
                 ),
            ))
            ->add('salaireHoraire', 'number')
            ->add('joursGarde', 'integer')
            ->add('heuresHebdo', 'integer')
            ->add('typeId')
            ->add('contratId')
            ;
    }

    public function getName()
    {
        return 'contrats';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}