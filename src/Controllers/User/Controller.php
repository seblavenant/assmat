<?php

namespace Assmat\Controllers\User;

use Twig_Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;
use Puzzle\Configuration;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Assmat\DataSource\Forms;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\Services\Form;
use Assmat\DataSource\DataTransferObjects as DTO;

class Controller
{
    private
        $twig,
        $request,
        $configuration,
        $urlGenerator,
        $formFactory,
        $contactForm,
        $formErrors,
        $contactRepository,
        $passwordEncoder,
        $mailer;

    public function __construct(
        Twig_Environment $twig,
        Request $request,
        Configuration $configuration,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory,
        Forms\Contact $contactForm,
        Form\Errors $formErrors,
        Repositories\Contact $contactRepository,
        PasswordEncoderInterface $passwordEncoder,
        $mailer
    ){
        $this->twig = $twig;
        $this->request = $request;
        $this->configuration = $configuration;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->contactForm = $contactForm;
        $this->formErrors = $formErrors;
        $this->contactRepository = $contactRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
    }

    public function lostpassEditAction()
    {
        return new Response($this->twig->render('user/lostpass_edit.html.twig'));
    }

    public function lostpassSendAction()
    {
        $email = $this->request->get('email');
        $error = null;

        if(!$email = filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $error = 'Cette adresse email est invalide';
        }

        $contact = $this->contactRepository->findFromEmail($email);

        if($contact instanceof Domains\Contact)
        {
            $newPassword = $this->createHash();
            $contact->savePassword($this->passwordEncoder->encodePassword($newPassword, $this->configuration->readRequired('app/salt')));
            $contact->persist($this->contactRepository);
            $this->sendMailToContact(
                $contact,
                'user/lostpass_mail.html.twig',
                'Récupération de mot de passe',
                array('password' => $newPassword)
            );
        }

        $responseData = array(
            'msg' => $error === null ? 'Votre demande de récupération de mot de passe a bien été prise en compte' : $error,
            'data' => array(),
        );

        if($error !== null)
        {
            $responseData['location'] = $this->urlGenerator->generate('user_login');
        }

        return new JsonResponse($responseData, $error === null ? 200 : 400);
    }

    public function newAction()
    {
        $form = $this->formFactory->create($this->contactForm);

        return new Response($this->twig->render('user/new.html.twig', array(
            'form' => $form->createView(),
        )));
    }

    public function createAction()
    {
        $form = $this->formFactory->create($this->contactForm);

        $form->bind($this->request);

        if($form->isValid())
        {
            $email = $form->get('email')->getData();
            $contact = $this->contactRepository->findFromEmail($email);

            if($contact instanceof Domains\Contact)
            {
                return new JsonResponse(
                    array(
                        'msg' => 'Il existe déjà un compte avec cet email',
                        'data' => array(),
                    ),
                    400
                );
            }

            $contactDTO = new DTO\Contact();
            $contactDTO->email = $email;
            $contactDTO->nom = $form->get('nom')->getData();
            $contactDTO->prenom = $form->get('prenom')->getData();
            $contactDTO->adresse = $form->get('adresse')->getData();
            $contactDTO->codePostal = $form->get('codePostal')->getData();
            $contactDTO->ville = $form->get('ville')->getData();
            $contactDTO->authCode = $this->createHash();

            $password = $this->createHash();
            $contactDTO->password = $this->passwordEncoder->encodePassword($password, $this->configuration->readRequired('app/salt'));
            $contact = (new Domains\Contact($contactDTO))->persist($this->contactRepository);

            $this->sendMailToContact(
                $contact,
                'user/new_mail.html.twig',
                'Création de compte',
                array('password' => $password)
            );

            $response = new JsonResponse(
                array(
                    'msg' => 'Profil créé',
                    'data' => array(),
                    'location' => $this->urlGenerator->generate('user_login'),
                ),
                200
            );
        }
        else
        {
            $response = new JsonResponse(
                array(
                    'msg' => 'Une erreur s\'est produite lors de la création',
                    'data' => $this->formErrors->getMessages($form),
                ),
                400
            );
        }

        return $response;
    }

    private function sendMailToContact(Domains\Contact $contact, $template, $subject, array $parameters = array())
    {
        $messageBody = $this->twig->render($template, array(
            'contact' => $contact,
            'parameters' => $parameters,
            'baseUrl' => $this->configuration->readRequired('app/baseUrl'),
        ));

        $this->sendMail($contact->getEmail(), $subject, $messageBody);
    }

    private function createHash()
    {
        return substr(hash('sha512', uniqid()), 0, 20);
    }

    private function sendMail($email, $subject, $messageBody)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('assmat@s3b.fr'))
            ->setTo(array($email))
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }
}