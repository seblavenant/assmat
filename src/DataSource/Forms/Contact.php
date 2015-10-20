<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;

class Contact extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'contacts.email',
                'required' => true,
            ))
            ->add('nom', 'text', array(
                'label' => 'contacts.nom',
                'required' => true,
                'constraints' => array(
                     new Constraints\NotBlank(),
                 ),
            ))
            ->add('prenom', 'text', array(
                'label' => 'contacts.prenom',
                'required' => true,
                'constraints' => array(
                     new Constraints\NotBlank(),
                 ),
            ))
            ->add('adresse', 'text', array(
                'label' => 'contacts.adresse',
                'required' => false,
            ))
            ->add('codePostal', 'text', array(
                'label' => 'contacts.codePostal',
                'required' => false,
            ))
            ->add('ville', 'text', array(
                'label' => 'contacts.ville',
                'required' => false,
            ))
            ;

        return $builder;
    }

    public function getName()
    {
        return 'contacts';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }
}