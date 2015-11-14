<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Indemnite extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('montant', 'text', array(
            'label' => sprintf('contrats.indemnites.%s.montant', $builder->getName()),
            'constraints' => array(
                new Constraints\NotBlank(),
            )
        ));
    }

    public function getName()
    {
        return 'indemnites';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
             'data_class' => 'Assmat\DataSource\Domains\Indemnite',
        ));
    }
}
