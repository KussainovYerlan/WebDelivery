<?php

namespace App\Service;


use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class AuthService
{
    private $mailer;
    private $templating;
    private $generator;
    private $manager;
    private $passwordEncoder;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, RouterInterface $generator, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->generator = $generator;
        $this->manager = $manager;
        $this->passwordEncoder = $encoder;
    }

    public function generate()
    {
        $token = time() . '_' . uniqid("", TRUE);

        return $token;
    }

    public function register (User $user, string $domen)
    {
        $repository = $this->manager->getRepository(User::class);
        while (1)
        {
            $token = $this->generate();
            $user_check = $repository->getUserByToken($token);
            if (!$user_check)
            {
                $user->setToken($token);
                break;
            }
        }
        // encode the plain password
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            )
        );

        $this->manager->persist($user);
        $this->manager->flush();


        $this->sendEmail($domen, $user,'email/registration.html.twig');
    }

    public function sendEmail(string $domen, User $user, $template)
    {
        $url = $domen . $this->generator->generate('activation', ['token' => $user->getToken()]);

        $message = (new \Swift_Message('Registration'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    $template,
                    [
                        'name' => $user->getLogin(),
                        'token' => $url,
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

}