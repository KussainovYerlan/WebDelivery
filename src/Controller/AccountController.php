<?php

namespace App\Controller;

use App\Entity\Checkout;
use App\Entity\Seller;
use App\Entity\SellerRequests;
use App\Entity\User;
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

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->persistToTable($user);
            $this->addFlash('notice', 'Вы успешно отредактивовали профиль');
            return $this->redirectToRoute('profile');
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

        if ($form->isSubmitted() && $form->isValid()) {

            $this->service->changePassword($form, $user);
            $this->addFlash('notice', 'Вы успешно изменили пароль');
            return $this->redirectToRoute('profile');
        }

        return $this->render('account/edit_password.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);
    }

    /**
     * @Route("/account/myhistory", name="myhistory")
     */
    public function myHistoryAction(Request $request)
    {
        $orders = $this->getDoctrine()->getRepository(Checkout::class)
            ->findByUser($this->getUser()->getId(), $request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($orders->count() / 4);

        return $this->render('account/history.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'orders' => $orders
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
    public function sellerOrdersListAction(Request $request)
    {

        $user = $this->getUser();
        $sellerId = $user->getSeller()->getId();
        $orders = $this->getDoctrine()->getRepository(Checkout::class)
            ->findBySeller($sellerId, $request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($orders->count() / 4);

        return $this->render('account/seller_orders.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/account/seller/orders/submit", name="seller_orders_submit")
     */
    public function sellerOrdersSubmitAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $order = $this->getDoctrine()->getRepository(Checkout::class)->find($id);
                $this->denyAccessUnlessGranted('submit', $order);
                $order->setStatus(Checkout::STATUS_ACCEPT);
                $this->service->persistToTable($order);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/orders/cancel", name="seller_orders_cancel")
     */
    public function sellerOrdersCancelAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $order = $this->getDoctrine()->getRepository(Checkout::class)->find($id);
                $this->denyAccessUnlessGranted('submit', $order);
                $order->setStatus(Checkout::STATUS_CANCEL);
                $this->service->persistToTable($order);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/request", name="requests")
     */
    public function sellerRequestsListAction(Request $request)
    {

        $sellerId = $this->getUser()->getSeller()->getId();
        $requests = $this->getDoctrine()->getRepository(SellerRequests::class)
            ->findBySeller($sellerId, $request->get('page'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($requests->count() / 4);

        return $this->render('account/requests.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/account/seller/managers", name="managers")
     */
    public function sellerManagersListAction(Request $request)
    {

        $managersClear = $this->service->getManagers($this->getUser(), $request);

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($managersClear->count() / 4);

        return $this->render('account/managers.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'managers' => $managersClear,
            'directory' => $this->getParameter('request_doc_directory')
        ]);
    }

    /**
     * @Route("/account/seller/request/submit", name="request_submit")
     */
    public function sellerRequestSubmitAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $this->service->submit($id);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/seller/request/cancel", name="request_cancel")
     */
    public function sellerRequestCancelAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $this->service->cancel($id);

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/account/sellers/list", name="sellers_choice")
     */
    public function choiceSellerAction(Request $request)
    {
        $sellers = $this->getDoctrine()->getRepository(Seller::class)
            ->findByNamePaginate($request->get('page'), $request->get('search'));

        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($sellers->count() / 4);

        return $this->render('account/sellers.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'sellers' => $sellers
        ]);
    }

}
