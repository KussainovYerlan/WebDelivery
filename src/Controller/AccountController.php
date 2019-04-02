<?php

namespace App\Controller;

use App\Entity\DeliveryOrder;
use App\Entity\SellerRequests;
use App\Form\ChangePasswordType;
use App\Form\EditProfileType;
use App\Service\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountController extends AbstractController
{
    private $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/account/profile", name="profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/account/edit/profile", name="edit_profile")
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
     * @Route("/account/edit/password", name="password_edit")
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
     * @Route("/account/myhistory", name="myhistory")
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
     * @Route("/account/mydiscounts", name="mydiscounts")
     */
    public function myDiscountsAction()
    {
        $user = $this->getUser();
        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/account/seller/orders", name="seller_orders")
     */
    public function sellerOrdersListAction()
    {
        $this->denyAccessUnlessGranted('view', new DeliveryOrder());
        $user = $this->getUser();
        $orders = $user->getSeller()->getDeliveryOrders();

        return $this->render('account/seller_orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/account/seller/orders/submit", name="seller_orders_submit")
     */
    public function sellerOrdersSubmitAction(Request $request)
    {
        if(($id = $request->request->get('id')) == true){
            $order = $this->getDoctrine()->getRepository(DeliveryOrder::class)->find($id);
            $order->setStatus(DeliveryOrder::STATUS_ACCEPT);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($order);
            $manager->flush();

            return new JsonResponse(['message' => 'Done'], 200);

        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/orders/cancel", name="seller_orders_cancel")
     */
    public function sellerOrdersCancelAction(Request $request)
    {
        if(($id = $request->request->get('id')) == true){
            $order = $this->getDoctrine()->getRepository(DeliveryOrder::class)->find($id);
            $order->setStatus(DeliveryOrder::STATUS_CANCEL);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($order);
            $manager->flush();

            return new JsonResponse(['message' => 'Done'], 200);

        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/request", name="requests")
     */
    public function sellerRequestsListAction()
    {

        $seller = $this->getUser()->getSeller();
        $requests = $seller->getRequests();

        return $this->render('account/requests.html.twig', [
            'requests' => $requests,
        ]);
    }

    /**
     * @Route("/account/seller/managers", name="managers")
     */
    public function sellerManagersListAction()
    {

        $managers = $this->getUser()->getSeller()->getUsers();

        return $this->render('account/requests.html.twig', [
            'managers' => $managers,
        ]);
    }

    /**
     * @Route("/account/seller/request/submit", name="request_submit")
     */
    public function sellerRequestSubmitAction(Request $request)
    {

        if(($id = $request->request->get('id')) == true){

            $this->service->submit($id);

            return new JsonResponse(['message' => 'Done'], 200);

        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/request/cancel", name="request_cancel")
     */
    public function sellerRequestCancelAction(Request $request)
    {

        if(($id = $request->request->get('id')) == true){

            $this->service->cancel($id);

            return new JsonResponse(['message' => 'Done'], 200);

        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

}
