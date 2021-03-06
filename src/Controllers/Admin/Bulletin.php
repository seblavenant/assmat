<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;
use Symfony\Component\HttpFoundation\Request;
use Assmat\Services;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Assmat\DataSource\Constants;

class Bulletin
{
    private
        $twig,
        $request,
        $security,
        $urlGenerator,
        $bulletinRepository,
        $evenementRepository,
        $contratRepository,
        $bulletinBuilderFromEvenements,
        $bulletinBuilderFromLignes;

    public function __construct(
        \Twig_Environment $twig,
        Request $request,
        SecurityContextInterface $security,
        UrlGeneratorInterface $urlGenerator,
        Repositories\Bulletin $bulletinRepository,
        Repositories\Evenement $evenementRepository,
        Repositories\Contrat $contratRepository,
        Services\Bulletin\Builders\FromEvenements $bulletinBuilderFromEvenements,
        Services\Bulletin\Builders\FromLignes $bulletinBuilderFromLignes
    )
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->bulletinRepository = $bulletinRepository;
        $this->evenementRepository = $evenementRepository;
        $this->contratRepository = $contratRepository;
        $this->bulletinBuilderFromEvenements = $bulletinBuilderFromEvenements;
        $this->bulletinBuilderFromLignes = $bulletinBuilderFromLignes;
        $this->urlGenerator = $urlGenerator;
    }

    public function indexAction($contratId)
    {
        $bulletins = $this->bulletinRepository->findFromContrat($contratId);

        return new Response($this->twig->render('admin/bulletins/list.html.twig', array(
            'bulletins' => $bulletins,
            'contratId' => $contratId,
        )));
    }

    public function newAction($contratId)
    {
        $this->validateDate();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $bulletin = $this->bulletinRepository->findOneFromContratAndDate($contratId, $annee, $mois);
        if($bulletin instanceof Domains\Bulletin)
        {
            return new RedirectResponse($this->urlGenerator->generate('admin_bulletins_read', array('id' => $bulletin->getId())));
        }

        $contrat = $this->contratRepository->find($contratId);
        $contrat->validateContactAutorisation($this->getContact());
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new \DateTime(sprintf('%d-%d-01', $annee, $mois)), true);
        $bulletin = $this->bulletinBuilderFromEvenements->build($contrat, $evenements, $annee, $mois);

        return new Response($this->twig->render('admin/bulletins/read.html.twig', array(
            'contrat' => $contrat,
            'evenements' => $this->evenementRepository->findAllFromContrat($contratId, new \DateTime(sprintf('%d-%d-01', $annee, $mois))),
            'annee' => $annee,
            'mois' => $mois,
            'bulletin' => $bulletin,
            'saveEnable' => true,
            'editable' => false,
        )));
    }

    public function createAction($contratId)
    {
        $this->validateDate();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $contrat = $this->contratRepository->find($contratId);
        $contrat->validateIsGrantedEmployeur($this->getContact());

        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new \DateTime(sprintf('%d-%d-01', $annee, $mois)), true);
        $bulletin = $this->bulletinBuilderFromEvenements->build($contrat, $evenements, $annee, $mois);

        try
        {
            $bulletin->persist($this->bulletinRepository);

            return new RedirectResponse($this->urlGenerator->generate('admin_bulletins_read', array('id' => $bulletin->getId())));
        }
        catch(\RuntimeException $e)
        {
            return new RedirectResponse($this->urlGenerator->generate('admin_bulletins_new', array('contratId' => $contratId, 'annee' => $annee, 'mois' => $mois)));
        }
    }

    public function readAction($id)
    {
        $bulletinHtml = $this->renderBulletin($id, 'admin/bulletins/read.html.twig');

        return new Response($bulletinHtml);
    }

    public function printAction($id)
    {
        $bulletinHtml = $this->renderBulletin($id, 'admin/bulletins/print.html.twig');

        $bulletinPdf  = (new Pdf('/usr/local/bin/wkhtmltopdf.sh'))->getOutputFromHtml(utf8_decode($bulletinHtml));

        $headers = array(
            'Content-Disposition' => 'attachment; filename="bulletin.pdf"',
            'Content-Type' => 'application/pdf',
            'Content-Length' => strlen($bulletinPdf),
        );

        return new Response($bulletinPdf, 200, $headers);
    }

    public function lignesAction($id)
    {
        $bulletin = $this->bulletinRepository->find($id);
        
        if(!$bulletin instanceof Domains\Bulletin)
        {
            throw new \Exception('Aucun bulletin ne correspond à cet identifiant');
        }
        
        $lignes = [];
        foreach($bulletin->getLignes() as $ligne)
        {
            $lignes[] = $ligne->toArray();
        }
        
        $lignes[] = [
            'typeId' => Constants\Lignes\Type::SALAIRE_NET,
            'valeur' => number_format($bulletin->getSalaireNet(), 2, '.', ''),
        ];
        
        return new JsonResponse($lignes);
    }
    
    public function updateAction($id)
    {
        $lignes = $this->request->get('lignes');
        
        $bulletin = $this->bulletinRepository->find($id);
        $this->bulletinBuilderFromLignes->build($bulletin, $lignes);

        return new JsonResponse(
            array(
                'msg' => 'Bulletin modifié',
                'data' => array(),
            )
            , 200
        );
    }
    
    private function renderBulletin($id, $view)
    {
        $bulletin = $this->bulletinRepository->find($id);

        if(!$bulletin instanceof Domains\Bulletin)
        {
            throw new \Exception('Aucun bulletin ne correspond à cet identifiant');
        }

        $bulletin->getContrat()->validateContactAutorisation($this->getContact());

        return $this->twig->render($view, array(
            'contrat' => $bulletin->getContrat(),
            'evenements' => $this->evenementRepository->findAllFromContrat($bulletin->getContrat()->getId(), new \DateTime(sprintf('%d-%d-01', $bulletin->getAnnee(), $bulletin->getMois()))),
            'annee' => $bulletin->getAnnee(),
            'mois' => $bulletin->getMois(),
            'bulletin' => $bulletin,
            'saveEnable' => false,
            'editable' => true,
        ));
    }

    private function validateDate()
    {
        if(!$this->request->get('mois') || !$this->request->get('annee'))
        {
            throw new \Exception('Les parametres "mois" et "annee" sont requis !');
        }
    }

    private function getContact()
    {
        return $this->security->getToken()->getUser()->getContact();
    }
}