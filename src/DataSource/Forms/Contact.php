<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Contact extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'contacts.email',
                'required' => true,
                'constraints' => array(
                    new Constraints\Email(),
                )
            ))
            ->add('nom', 'text', array(
                'label' => 'contacts.nom',
                'required' => false,
            ))
            ->add('prenom', 'text', array(
                'label' => 'contacts.prenom',
                'required' => false
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
            ->add('authCode', 'text', array(
                'label' => 'contacts.authCode',
                'attr' => ['readonly' => true],
                'required' => false,
            ))
            ->add('id', 'hidden')
            ;

        return $builder;
    }

    public function getName()
    {
        return 'contacts';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }
}