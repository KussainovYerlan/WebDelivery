<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\User;
use App\Form\ImportTableType;
use App\Form\SearchProductType;
use App\Service\TokenGenerator;
use App\Service\ProductImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('index/index.html.twig', [
        ]);
    }

}
