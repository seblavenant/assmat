<?php

namespace Assmat\DataSource\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;

class Contrat extends AbstractType
{
    private
        $employesRepository;

    public function __construct(Repositories\Employe $employeRepository)
    {
        $this->employeRepository = $employeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'label' => 'contrats.nom',
                'required' => true,
                'constraints' => array(
                     new Constraints\NotBlank(),
                 ),
            ))
            ->add('salaireHoraire', 'text', array(
                'label' => 'contrats.salaireHoraire',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Regex(array(
                        'pattern' => '/^\d*\.?\d*$/i',
                        'message' => 'Le format du montant est invalide (attendu 0.00)'
                    )),
                )
            ))
            ->add('joursGarde', 'choice', array(
                'label' => 'contrats.joursGarde',
                'required' => true,
                'choices' => range(1, 7),
                'placeholder' => '-- Sélectionnez un nombre de jours --',
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('heuresHebdo', 'integer', array(
                'label' => 'contrats.heuresHebdo',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Range(array('min' => 0, 'max' => 99)),
                )
            ))
            ->add('nombreSemainesAn', 'integer', array(
                'label' => 'contrats.nombreSemainesAn',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Range(array('min' => 0, 'max' => 52)),
                )
            ))
            ->add('typeId', 'choice', array(
                'label' => 'contrats.typeId',
                'required' => true,
                'choices' => array(
                    Constants\Contrats\Salaire::MENSUALISE => 'Mensualisé',
                ),
                'placeholder' => '-- Sélectionnez un type de contrat --',
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('employeId', 'choice', array(
                 'label' => 'contrats.employeId',
                 'required' => true,
                 'choices' => $this->retrieveEmployees($options),
                 'placeholder' => '-- Sélectionnez un employé --',
                 'constraints' => array(
                      new Constraints\NotBlank(),
                 )
            ))
            ->add('employeurId', 'hidden')
            ;
    }

    public function getName()
    {
        return 'contrats';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'employeur' => null,
        ));
    }

    private function retrieveEmployees(array $options)
    {
        if(! array_key_exists('employeur', $options) || ! $options['employeur'] instanceof Domains\Employeur)
        {
            return array();
        }

        $employes = $this->employeRepository->findFromEmployeur($options['employeur']->getId());

        $employesArray = array();
        foreach($employes as $employe)
        {
            if($employe instanceof Domains\Employe)
            {
                $employesArray[$employe->getId()] = sprintf('%s %s', $employe->getContact()->getPrenom(), $employe->getContact()->getNom());
            }
        }

        return $employesArray;
    }
}