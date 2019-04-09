<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\Checkout;
use App\Entity\User;
use App\Form\ImportTableType;
use App\Form\SearchProductType;
use App\Service\TokenGenerator;
use App\Service\ProductImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    /**
     * @var ProductImportService
     */
    private $productImportService;

    public function __construct(ProductImportService $productImportService)
    {
        $this->productImportService = $productImportService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request):Response
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
