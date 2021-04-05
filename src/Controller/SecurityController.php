<?php

namespace App\Controller;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\RegisterType;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // utilise le composant pour encoder les passwords
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class SecurityController extends AbstractController
{
    #[Route('/security', name: 'security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }


    #[Route('/login', name: 'app_login')]

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]


    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function regsiter(Request $request,
                             UserPasswordEncoderInterface $encoder, // t'as besoin du composant d'encodage des mots de passe
                             MailerInterface $mailer) // t'as besoin du composant pour l'envoi de mail
            {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {   $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            $encoded = $encoder->encodePassword($user, $user->getPassword()); // je t'encode ton mot de passe
            $user->setPassword($encoded); // je t'envoie le mot de passe encodé

            /** envoi d'email */
            $email = new Email();
            $email->addTo($user->getEmail());
            $email->subject('Bienvenue sur le test');
            $email->Html('<p>test</p>');
            $email->from('doej23398@gmail.com');
            $mailer->send($email);
            /** end envoi  */

            $em->flush();
            return new Response('Le formulaire a été soumis...');

        }


        return $this->render('security/register.html.twig', ['form' =>$form->createView()
        ]);


    }





}
