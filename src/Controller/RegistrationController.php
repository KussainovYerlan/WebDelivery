<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


class RegistrationController extends AbstractController
{
    private $service;

    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            /* Redirect the user to the homepage */
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $ps = $user->getPassword();
        $enc = $passwordEncoder->encodePassword($user, $user->getPassword());


        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->register($user, $request->getSchemeAndHttpHost(), $form->get('plainPassword')->getData());


            $this->addFlash('notice', 'Вы успешно зарегистрировались, проверьте ваш email! ');
            return $this->redirectToRoute('app_login');
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
