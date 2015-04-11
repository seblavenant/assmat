<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Request;

class Bulletin
{
    private
        $twig,
        $formFactory,
        $bulletinRepository,
        $evenementRepository;

    public function __construct(\Twig_Environment $twig, Request $request, FormFactoryInterface $formFactory, Repositories\Bulletin $bulletinRepository, Repositories\Evenement $evenementRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->formFactory = $formFactory;
        $this->bulletinRepository = $bulletinRepository;
        $this->evenementRepository = $evenementRepository;
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
        $this->validateRangeDateParams();

        $bulletinForm = $this->formFactory->create(new Forms\Bulletin());
        $evenements = $this->evenementRepository->findFromContrat($contratId);

        return new Response($this->twig->render('admin/bulletins/new.html.twig', array(
            'bulletinForm' => $bulletinForm->createView(),
            'contratId' => $contratId,
            'evenements' => $evenements,
            'mois' => $this->request->get('mois'),
            'annee' => $this->request->get('annee'),
        )));
    }

    private function validateRangeDateParams()
    {
        if(! in_array((int) $this->request->get('mois'), range(1, 12)))
        {
            throw new \Exception('Le mois ' . $this->request->get('mois') . ' est invalide !');
        }

        if((int) $this->request->get('annee') < 2000)
        {
            throw new \Exception('L\'année ' . $this->request->get('annee') . ' est invalide !');
        }

    }
}