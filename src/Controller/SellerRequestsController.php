<?php

namespace App\Controller;

use App\Entity\SellerRequests;
use App\Form\SellerRequestsType;
use App\Repository\SellerRequestsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//JUST FOR THE TEST
/**
 * @Route("/seller/requests")
 */
class SellerRequestsController extends AbstractController
{
    /**
     * @Route("/new", name="seller_requests_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sellerRequest = new SellerRequests();
        $form = $this->createForm(SellerRequestsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ($form->get('file')->getData() || $form->get('resume')->getData())) {
            if ($file = $form->get('file')->getData())
            {
                $file = $form->get('file')->getData();
                $fileName = time() . uniqid() . '.' . $file->guessExtension();
                $file->move(
                    str_replace('/src/Controller', '', __DIR__ . '/public/assets/request_doc'),
                    $fileName
                );
                $sellerRequest->setFile($fileName);
            }
            $sellerRequest->setSeller($form->get('seller')->getData());
            $sellerRequest->setUser($this->getUser());
            $sellerRequest->setFirstName($form->get('firstName')->getData());
            $sellerRequest->setLastName($form->get('lastName')->getData());
            $sellerRequest->setresume($form->get('resume')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sellerRequest);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('seller_requests/new.html.twig', [
            'seller_request' => $sellerRequest,
            'form' => $form->createView(),
        ]);
    }
}
