<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Request;

class Bulletin
{
    private
        $twig,
        $request,
        $bulletinRepository;

    public function __construct(\Twig_Environment $twig, Request $request, Repositories\Bulletin $bulletinRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
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
}