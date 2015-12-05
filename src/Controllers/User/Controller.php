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

class Controller
{
    private
        $twig,
        $request,
        $configuration,
        $contactRepository,
        $passwordEncoder,
        $mailer;

    public function __construct(Twig_Environment $twig, Request $request, Configuration $configuration, Repositories\Contact $contactRepository, PasswordEncoderInterface $passwordEncoder, $mailer)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->configuration = $configuration;
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

        if (! $email = filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $error = 'Cette adresse email est invalide';
        }

        $this->sendMailToContact($email);

        return new JsonResponse(
            array(
                'msg' => $error === null ? 'Votre demande de récupération de mot de passe a bien été prise en compte' : $error,
                'data' => array(),
            )
            , $error=== null ? 200 : 400
        );
    }

    private function sendMailToContact($email)
    {
        $contact = $this->contactRepository->findFromEmail($email);

        if($contact instanceof Domains\Contact)
        {
            $password = $this->createNewPassword($contact);
            $messageBody = $this->twig->render('user/lostpass_mail.html.twig', array(
                'contact' => $contact,
                'password' => $password,
                'baseUrl' => $this->configuration->readRequired('app/baseUrl'),
            ));

            $this->sendMail($email, $messageBody);
        }
    }

    private function createNewPassword(Domains\Contact $contact)
    {
        $newPassword = substr(hash('sha512', uniqid()), 0, 20);

        $contact->savePassword($this->passwordEncoder->encodePassword($newPassword, $this->configuration->readRequired('app/salt')));
        $contact->persist($this->contactRepository);

        return $newPassword;
    }

    private function sendMail($email, $messageBody)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Récupération du mot de passe')
            ->setFrom(array('assmat@s3b.fr'))
            ->setTo(array($email))
            ->setBody($messageBody);

        $this->mailer->send($message);
    }
}