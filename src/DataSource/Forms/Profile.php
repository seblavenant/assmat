<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Profile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contact', new Contact());
    }

    public function getName()
    {
        return 'profiles';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }
}