<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Entity\User;
use App\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {

        return $this->render('index/index.html.twig', [
        ]);
    }
}
