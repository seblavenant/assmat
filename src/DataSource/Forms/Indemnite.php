<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Repositories\LigneTemplate;

class Indemnite extends AbstractType
{
    private
        $ligneTemplateRepository;

    public function __construct(Repositories\LigneTemplate $ligneTemplateRepository)
    {
        $this->ligneTemplateRepository = $ligneTemplateRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addIndemnites($builder, $options);
    }

    public function getName()
    {
        return 'indemnites';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }

    private function addIndemnites(FormBuilderInterface $builder, array $options)
    {
        $indemnites = $this->ligneTemplateRepository->findFromContexts(array(Constants\Lignes\Context::INDEMNITE));

        foreach($indemnites as $indemnite)
        {
            $builder->add($indemnite->getTypeId(), 'text', array(
                'label' => sprintf('%s.%s', $this->getName(), $indemnite->getTypeId()),
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ));
        }
    }
}