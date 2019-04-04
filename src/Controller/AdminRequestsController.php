<?php

namespace App\Controller;

use App\Entity\AdminRequests;
use App\Form\AdminRequestsType;
use App\Repository\AdminRequestsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/requests")
 */
class AdminRequestsController extends AbstractController
{
    /**
     * @Route("/new", name="admin_requests_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $adminRequest = new AdminRequests();
        $form = $this->createForm(AdminRequestsType::class, $adminRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $form->get('company_file')->getData())
            {
                $file = $form->get('company_file')->getData();
                $fileName = time() . uniqid() . '.' . $file->guessExtension();
                $file->move(
                    str_replace('/src/Controller', '', __DIR__ . '/public/assets/request_doc'),
                    $fileName
                );
                $adminRequest->setCompanyFile($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adminRequest);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('admin_requests/new.html.twig', [
            'admin_request' => $adminRequest,
            'form' => $form->createView(),
        ]);
    }

}
