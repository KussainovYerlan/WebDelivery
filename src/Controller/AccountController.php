<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountController extends AbstractController
{

    /**
     * @Route("/profile", name="profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/edit/profile", name="edit_profile")
     */
    public function profileEditAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            return $this->render('account/success.html.twig', [
                'message' => 'Данные успешно отредактированы'
            ]);
        }

        return $this->render('account/edit_profile.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);

    }



    /**
     * @Route("/profile/edit/password", name="password_edit")
     */
    public function passwordEditAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $old_pwd = $form->get('old_pass')->getData();
            $new_pwd = $form->get('new_password')->getData();
            $checkPass = $passwordEncoder->isPasswordValid($user, $old_pwd);
            if ($checkPass)
            {
                $new_pwd = $passwordEncoder->encodePassword($user, $new_pwd);
                $user->setPassword($new_pwd);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                return $this->render('account/success.html.twig',
                    [
                        'message' => 'Вы успешно изменили пароль'
                    ]);
            } else {
                return $this->render('account/edit_password.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Неправильный пароль.'
                ]);
            }
        }

        return $this->render('account/edit_password.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);
    }

    /**
     * @Route("/myhistory", name="myhistory")
     */
    public function myHistoryAction()
    {
        $user = $this->getUser();
        $orders = $user->getDeliveryOrders();
        return $this->render('account/history.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/mydiscounts", name="mydiscounts")
     */
    public function myDiscountsAction()
    {
        $user = $this->getUser();
        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }
}
