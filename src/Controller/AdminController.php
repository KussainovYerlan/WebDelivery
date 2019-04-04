<?php

namespace App\Controller;

use App\Entity\AdminRequests;
use App\Entity\User;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    const COUNT_USER = 10;

    private $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("", name="dashboard")
     */
    public function dashboardAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $newUsers = $this->service->newUserRegistry();
        $doneOrdersToday = $this->service->getDoneOrdersToday();
        return $this->render('admin/dashboard.html.twig', [
            'newUser' => $newUsers,
            'doneOrdersToday' => $doneOrdersToday
        ]);
    }

    /**
     * @Route("/users", name="users")
     */
    public function usersListAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findByLoginAndRole($request->get('page'), $request->get('search'), $request->get('role'));
        $thisPage = $request->get('page') ?: 1;

        $maxPages = ceil($users->count() / 10);

        return $this->render('admin/users.html.twig', [
            'thisPage' => $thisPage,
            'maxPages' => $maxPages,
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/delete", name="user_delete")
     */
    public function deleteUser(Request $request)
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {
                $user = $this->getDoctrine()->getRepository(User::class)->find($id);
                $manager = $this->getDoctrine()->getManager();
                $manager->remove($user);
                $manager->flush();

                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/data/registry", name="data_count_users")
     */
    public function getRegistryUser(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $userRegistryCount = $this->service->chartUserRegistry(self::COUNT_USER);
            return new JsonResponse($userRegistryCount, 200);
        }

        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/requests", name="requests")
     */
    public function adminRequestsListAction()
    {
        $requests = $this->getDoctrine()->getRepository(AdminRequests::class)->findAll();

        return $this->render('admin/requests.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/requests/submit", name="requests_submit")
     */
    public function adminRequestsSubmitAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $adminRequest = $this->getDoctrine()->getRepository(AdminRequests::class)->find($id);
                $this->service->requestSubmit($adminRequest, $request->getSchemeAndHttpHost());
                return new JsonResponse(['message' => 'Done'], 200);

            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }

    /**
     * @Route("/requests/cancel", name="requests_cancel")
     */
    public function adminRequestsCancelAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            if (($id = $request->request->get('id')) == true) {

                $adminRequest = $this->getDoctrine()->getRepository(AdminRequests::class)->find($id);
                $this->service->requestCancel($adminRequest);
                return new JsonResponse(['message' => 'Done'], 200);
            }
        }
        return new JsonResponse(['message' => 'Update failure'], 404);
    }
}
