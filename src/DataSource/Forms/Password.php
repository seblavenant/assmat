<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Password extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', array(
                'type' => 'password',
                'label' => 'contacts.password',
                'invalid_message' => 'Les 2 mots de passes saisis ne sont pas identiques',
                'first_options'=> array('label' => 'Mot de passe'),
                'second_options'=> array('label' => 'Confirmation'),
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
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

    }
}