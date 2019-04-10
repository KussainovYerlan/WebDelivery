<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    /**
     * @Route("/seller/{id}/products", name="product_index", methods={"GET"})
     */
    public function index(int $id, Request $request): Response
    {
        $seller = $this->getDoctrine()->getRepository(Seller::class)->find($id);

        if ($seller)
        {
            $products = $this->getDoctrine()->getRepository(Product::class)
                ->searchProducts($request->get('query'), $seller->getId(), $request->get('page'));

            $thisPage = $request->get('page') ?: 1;

            $maxPages = ceil($products->count() / 4);
            return $this->render('product/index.html.twig', [
                'thisPage' => $thisPage,
                'maxPages' => $maxPages,
                'seller' => $seller,
                'products' => $products,
            ]);
        }
        
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
