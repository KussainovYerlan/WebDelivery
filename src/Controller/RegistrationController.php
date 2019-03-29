<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Service\TokenGenerator;


class RegistrationController extends AbstractController
{
    const TOKEN_LENGTH = 60;

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            /* Redirect the user to the homepage */
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $generator = new TokenGenerator();
            while (1)
            {
                $token = $generator->generate(self::TOKEN_LENGTH);
                $user_check = $repository->getUserByToken($token);
                if (!$user_check)
                {
                    $user->setToken($token);
                    break;
                }
            }
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $url = $request->getSchemeAndHttpHost() . $this->generateUrl('activation', array('token' => $user->getToken()));

            $message = (new \Swift_Message('Registration'))
                ->setFrom('delivery.dev@gamil.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/registration.html.twig',
                        [
                            'name' => $user->getLogin(),
                            'token' => $url,
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute('index');
            // do anything else you need here, like send an email

            //disable auto-login
            /*return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );*/
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
