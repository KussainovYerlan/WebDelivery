<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Entity\Checkout;
use App\Entity\User;
use App\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('seller', EntityType::class, [
                'label' => 'Магазин',
                'class' => Seller::class,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Выбрать'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $session->set('sellerId', $form->getData()['seller']->getId());
            $session->set('shoppingCart', '');

            return $this->redirectToRoute('product_index');
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
