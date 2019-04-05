<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\User;
use App\Form\ImportTableType;
use App\Service\TokenGenerator;
use App\Service\TableImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
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

        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();
        $form = $this->createForm(ImportTableType::class);
        $form->handleRequest($request);

        $sheetData='';
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->getData()['importFile'];
            $table = new TableImporter();
            $sheetData = $table->importCsv($file, $repository);
        }
        return $this->render('index/importcsv.html.twig', [
            'form' => $form->createView(),
            'sheetData' => $sheetData,
            'products' => $products,
        ]);
    }
}
