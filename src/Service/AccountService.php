<?php


namespace App\Service;


use App\Entity\SellerRequests;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class AccountService
{

    private $manager;
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function submit(int $id)
    {
        $sellerRequest = $this->manager->getRepository(SellerRequests::class)->find($id);
        if ($sellerRequest->getFile())
        {
            $filesystem = new Filesystem();
            $filesystem->remove(str_replace('/src/Service', '', __DIR__ . '/public/assets/request_doc/' . $sellerRequest->getFile()));
        }

        $user = $sellerRequest->getUser();
        $user->setRoles(['ROLE_SELLER_MANAGER']);
        $user->setSeller($sellerRequest->getSeller());
        $this->manager->persist($user);
        $this->manager->remove($sellerRequest);
        $this->manager->flush();

        $message = (new \Swift_Message('Заявка на роль менеджера.'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/request_submit.html.twig',
                    [
                        'seller' => $user->getSeller(),
                        'name' => $user->getLogin(),
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function cancel(int $id)
    {
        $sellerRequest = $this->manager->getRepository(SellerRequests::class)->find($id);
        if ($sellerRequest->getFile())
        {
            $filesystem = new Filesystem();
            $filesystem->remove(str_replace('/src/Service', '', __DIR__ . '/public/assets/request_doc/' . $sellerRequest->getFile()));
        }

        $user = $sellerRequest->getUser();
        $this->manager->remove($sellerRequest);
        $this->manager->flush();

        $message = (new \Swift_Message('Заявка на роль менеджера.'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/request_cancel.html.twig',
                    [
                        'seller' => $user->getSeller(),
                        'name' => $user->getLogin(),
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}