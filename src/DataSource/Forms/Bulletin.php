<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Bulletin extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('annee', 'choice',
                array(
                    'choices' => range(Date('Y') - 1, date('Y') + 2),
                ))
            ->add('mois', 'choice',
                array(
                    'choices' => range(1, 12),
                )
            );
    }

    public function getName()
    {
        return 'bulletin';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
//             'csrf_protection' => false,
        ));
    }
}