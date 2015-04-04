<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Forms;

class Bulletin
{
    private
        $twig,
        $formFactory,
        $bulletinRepository;

    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, Repositories\Bulletin $bulletinRepository)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->bulletinRepository = $bulletinRepository;
    }

    public function indexAction($contratId)
    {
        $bulletins = $this->bulletinRepository->findFromContrat($contratId);

        return new Response($this->twig->render('admin/bulletins/list.html.twig', array(
            'bulletins' => $bulletins,
        )));
    }

    public function readAction($id)
    {
        $bulletin = $this->bulletinRepository->find($id);

        return new Response($this->twig->render('admin/bulletins/read.html.twig', array(
            'bulletin' => $bulletin,
        )));
    }

    public function newAction($contratId)
    {
        $bulletinForm = $this->formFactory->create(new Forms\Bulletin());

        return new Response($this->twig->render('admin/bulletins/new.html.twig', array(
            'bulletinForm' => $bulletinForm->createView(),
            'contratId' => $contratId,
        )));
    }
}