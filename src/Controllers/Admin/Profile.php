<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Forms;

class Profile
{
    private
        $twig,
        $request,
        $security,
        $formFactory,
        $profileForm;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, FormFactoryInterface $formFactory, Forms\Profile $profileForm)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->profileForm = $profileForm;
    }


    public function editAction()
    {
        $form = $this->formFactory->create($this->profileForm);

        return new Response($this->twig->render('admin/profile/edit.html.twig', array(
            'form' => $form->createView(),
        )));
    }
}