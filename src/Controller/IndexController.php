<?php

namespace App\Controller;

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
        $gen = new TokenGenerator();
        dump($gen->generate());
        return $this->render('index/index.html.twig', [
        ]);
    }
}
