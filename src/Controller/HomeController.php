<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]

    public function home ()
   {
    return $this->render('ads/home.html.twig');
   }
}