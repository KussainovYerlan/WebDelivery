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
     * @Route("/", name="index")
     */

    /**
     * @var ProductImportService
     */
    private $productImportService;

    public function __construct(ProductImportService $productImportService)
    {
        $this->productImportService = $productImportService;
    }

    public function index(Request $request):Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }
    /**
     * @Route("/importcsv", name="importcsv")
     */
    public function importCsv(Request $request):Response
    {
        $form = $this->createForm(ImportTableType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->getData()['importFile'];
            $this->productImportService->importCsv($file);
        }
        return $this->render('index/importcsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/search", name="search")
     */
    public function searchProducts(Request $request):Response
    {
        $search = $request ->get('query');

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->searchProducts($search);

        return $this->render('index/search.html.twig', [
            'products' => $products,
        ]);
    }
}
