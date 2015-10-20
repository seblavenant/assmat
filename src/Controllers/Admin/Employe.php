<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\Services\Form;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Response;

class Employe
{
    private
        $twig,
        $request,
        $security,
        $urlGenerator,
        $formFactory,
        $formErrors,
        $employeForm;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, UrlGeneratorInterface $urlGenerator, FormFactoryInterface $formFactory, Form\Errors $formErrors, Forms\Employe $employeForm)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->formErrors = $formErrors;
        $this->employeForm = $employeForm;
    }

    public function indexAction()
    {
        return $this->newAction();
    }

    public function newAction()
    {
        $form = $this->formFactory->create($this->employeForm);

        return new Response($this->twig->render('admin/employes/new.html.twig', array(
            'form' => $form->createView(),
        )));
    }
}
