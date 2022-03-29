<?php

namespace App\Controller;

use App\Repository\AdsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdsController extends AbstractController
{
    #[Route('/ads', name: 'ads', methods: ['GET'])]
    public function ads(AdsRepository $adsRepository): Response
    {
        $ads = $adsRepository->findBy(['done' => false]);

        return $this->render('ads/ads.html.twig', [
            'ads' => $ads,
        ]);

    }
}
