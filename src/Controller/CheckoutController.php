<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Repository\ProductRepository;
use App\Entity\CheckoutProduct;
use App\Entity\Checkout;
use App\Form\CheckoutType;
use App\Repository\CheckoutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/checkout")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("/", name="checkout_index", methods={"GET"})
     */
    public function index(CheckoutRepository $checkoutRepository): Response
    {
        return $this->render('checkout/index.html.twig', [
            'checkouts' => $checkoutRepository->findAll(),
        ]);
    }

    /**
     * @Route("/cart", name="checkout_cart", methods={"POST"})
     */
    public function shoppingCart(Request $request)
    {
        $session = $request->getSession();
        $shoppingCart = json_decode($request->request->get('products'), true);
        $session->set('shoppingCart', $shoppingCart);
        return new Response();
    }

    /**
     * @Route("/new", name="checkout_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $checkout = new Checkout();

        $session = $request->getSession();

        $sellerId = $session->get('sellerId');
        $seller = $this->getDoctrine()
            ->getRepository(Seller::class)
            ->findOneById($sellerId)
        ;

        if ($session->get('userAddress'))
        {
            $checkout->setAddress($session->get('userAddress'));
        }

        $checkout->setSeller($seller);
        $checkout->setUser($this->getUser());

        $shoppingCart = $session->get('shoppingCart');
        $entityManager = $this->getDoctrine()->getManager();
        foreach($shoppingCart as $id => $count) {
            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findOneById($id)
            ;
            $checkoutProduct = new CheckoutProduct();
            $checkoutProduct->setProduct($product)
                ->setCount($count)
            ;
            $entityManager->persist($checkoutProduct);
            $checkout->addCheckoutProduct($checkoutProduct);
        }

        $form = $this->createForm(CheckoutType::class, $checkout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($checkout);
            $entityManager->flush();

            return $this->redirectToRoute('checkout_index');
        }

        return $this->render('checkout/new.html.twig', [
            'checkout' => $checkout,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="checkout_show", methods={"GET"})
     */
    public function show(Checkout $checkout): Response
    {
        return $this->render('checkout/show.html.twig', [
            'checkout' => $checkout,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="checkout_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Checkout $checkout): Response
    {
        $form = $this->createForm(CheckoutType::class, $checkout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('checkout_index', [
                'id' => $checkout->getId(),
            ]);
        }

        return $this->render('checkout/edit.html.twig', [
            'checkout' => $checkout,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="checkout_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Checkout $checkout): Response
    {
        if ($this->isCsrfTokenValid('delete'.$checkout->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($checkout);
            $entityManager->flush();
        }

        return $this->redirectToRoute('checkout_index');
    }
}
