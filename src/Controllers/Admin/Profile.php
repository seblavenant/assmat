<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Forms;
use Assmat\DataSource\Repositories;
use Assmat\Services\Form;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class Profile
{
    private
        $twig,
        $request,
        $security,
        $encoderFactory,
        $formFactory,
        $profileForm,
        $passwordForm,
        $formErrors,
        $employeRepository,
        $employeurRepository,
        $contactRepository;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, EncoderFactoryInterface $encoderFactory, FormFactoryInterface $formFactory, Forms\Profile $profileForm, Forms\Password $passwordForm, Form\Errors $formErrors, Repositories\Employe $employeRepository, Repositories\Employeur $employeurRepository, Repositories\Contact $contactRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->encoderFactory = $encoderFactory;
        $this->profileForm = $profileForm;
        $this->passwordForm = $passwordForm;
        $this->formErrors = $formErrors;
        $this->employeRepository = $employeRepository;
        $this->employeurRepository = $employeurRepository;
        $this->contactRepository = $contactRepository;
    }

    public function editAction()
    {
        $form = $this->formFactory->create($this->profileForm, $this->buildProfileFormData());

        return new Response($this->twig->render('admin/profile/edit.html.twig', array(
            'form' => $form->createView(),
        )));
    }

    public function updateAction()
    {
        $form = $this->formFactory->create($this->profileForm, $this->buildProfileFormData());

        $form->bind($this->request);

        if($form->isValid())
        {
            $contactForm = $form->get('contacts');
            $contactDTO = new DTO\Contact();
            $contactDTO->id = $contactForm->get('id')->getData();
            $contactDTO->email = $contactForm->get('email')->getData();
            $contactDTO->nom = $contactForm->get('nom')->getData();
            $contactDTO->prenom = $contactForm->get('prenom')->getData();
            $contactDTO->adresse = $contactForm->get('adresse')->getData();
            $contactDTO->codePostal = $contactForm->get('codePostal')->getData();
            $contactDTO->ville = $contactForm->get('ville')->getData();
            (new Domains\Contact($contactDTO))->persist($this->contactRepository);

            $employeForm = $form->get('employes');
            $employeDTO = new DTO\Employe();
            $employeDTO->id = $employeForm->get('id')->getData();
            $employeDTO->ssId = $employeForm->get('ssId')->getData();
            $employeDTO->contactId = $contactDTO->id;
            (new Domains\Employe($employeDTO))->persist($this->employeRepository);

            $employeurForm = $form->get('employeurs');
            $employeurDTO = new DTO\Employeur();
            $employeurDTO->id = $employeurForm->get('id')->getData();
            $employeurDTO->pajeEmploiId = $employeurForm->get('pajeEmploiId')->getData();
            $employeurDTO->contactId = $contactDTO->id;
            (new Domains\Employeur($employeurDTO))->persist($this->employeurRepository);

            $response = new JsonResponse(
                array(
                    'msg' => 'Profil modifié',
                    'data' => array(),
                )
                , 200
            );
        }
        else
        {
            $response = new JsonResponse(
                array(
                    'msg' => 'Une erreur s\'est produite lors de l\'enregistrement',
                    'data' => $this->formErrors->getMessages($form),
                )
                , 400
            );
        }

        return $response;
    }

    public function passwordEditAction()
    {
        $form = $this->formFactory->create($this->passwordForm);

        return new Response($this->twig->render('admin/profile/password/edit.html.twig', array(
            'form' => $form->createView(),
        )));
    }

    public function passwordUpdateAction()
    {
        $form = $this->formFactory->create($this->passwordForm);

        $form->bind($this->request);

        if($form->isValid())
        {
            $user = $this->security->getToken()->getUser();

            $encoder = $this->encoderFactory->getEncoder($user);
            $password = $encoder->encodePassword($form->get('password')->getData(), $user->getSalt());

            $contact = $user->getContact();

            $contact->savePassword($password);
            $contact->persist($this->contactRepository);

            $response = new JsonResponse(
                array(
                    'msg' => 'Mot de passe modifié',
                    'data' => array(),
                )
                , 200
            );
        }
        else
        {
            $response = new JsonResponse(
                array(
                    'msg' => 'Une erreur s\'est produite lors de l\'enregistrement',
                    'data' => $this->formErrors->getMessages($form),
                )
                , 400
            );
        }

        return $response;
    }

    private function buildProfileFormData()
    {
        $contactData = null;
        $employeData = null;
        $employeurData = null;

        $contact = $this->security->getToken()->getUser()->getContact();
        if($contact instanceof Domains\Contact)
        {
            $contactData = $contact->toArray();
        }
        $employeur = $this->employeurRepository->findFromContact($contact->getId());
        if($employeur instanceof Domains\Employeur)
        {
            $employeurData = $employeur->toArray();
        }
        $employe = $this->employeRepository->findFromContact($contact->getId());
        if($employe instanceof Domains\Employe)
        {
            $employeData = $employe->toArray();
        }

        return array(
            'contacts' => $contactData,
            'employeurs' => $employeurData,
            'employes' => $employeData,
        );
    }
}