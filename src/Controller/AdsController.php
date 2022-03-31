<?php

namespace App\Controller;

use App\Entity\Ads;
use App\Entity\Media;
use App\Form\AdTypeForm;
use App\Repository\AdsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdsController extends AbstractController
{
    #[Route('/ads', name: 'ads', methods: ['GET'])]
    public function ads(AdsRepository $adsRepository): Response
    {
        $ads = $adsRepository->findBy(['done' => false], ['date' => 'DESC']);

        return $this->render('ads/ads.html.twig', [ 'ads' => $ads]);

    }

    #[Route('/ad/{id}', name: 'ad', methods: ['GET'])]
    public function ad (AdsRepository $adRepository, $id)
    {
        $ad = $adRepository->findOneBy(["id" => $id]);

        return $this->render('ads/ad.html.twig', ['ad' => $ad]);
    }

    #[Route('/addAd', name: 'addAd', methods: ['GET', 'POST'])]
    public function addAd(Request $request, AdsRepository $adRepository, SluggerInterface $slugger): Response
    {
        $ad = new Ads();
        $media = new Media();

        $now = new \DateTime('now');
        $form = $this->createForm(AdTypeForm::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->get('media')->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename. '-' .uniqid().'.'.$mediaFile->guessExtension();
                $mediaFile->move(
                    $this->getParameter('pathUpload_directory'), $newFilename
                );
                $media->setName($newFilename);
                $ad->setMedia($media);
            }
            $ad->setDate($now);
            $ad->setDone(false);
            $adRepository->add($ad);

            return $this->redirectToRoute('ads');
        }
        return $this->render('ads/addAd.html.twig', ['adForm' => $form->createView()]);
    }

    #[Route('/adUpdate/{id}', name: 'adUpdate', methods: ['GET', 'POST'])]
    public function adUpdate($id, AdsRepository $adRepository, Request $request, SluggerInterface $slugger)
    {
        $ad = $adRepository->findOneBy(["id" => $id]);
        $ad->getDone();
        $media = new Media();

        $form = $this->createForm(AdTypeForm::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('media')->getData()) {
                $mediaFile = $form->get('media')->getData();
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $fileName = $safeFilename . '-' . uniqid() . '-' . $mediaFile->guessExtension();
                $mediaFile->move($this->getParameter('pathUpload_directory'), $fileName);

                $media->setName($fileName);
                $ad->setDone(false)
                    ->setMedia($media);
                $adRepository->add($ad);
            } else {
                $adRepository->add($ad);
            }
            return $this->redirectToRoute('ads');
        }
        return $this->render('ads/adUpdate.html.twig', [
            'adForm' => $form->createView(),
            'ad' => $ad
        ]);
    }

    #[Route('/deleteAd/{id}', name: 'deleteAd', methods: ['GET'])]
    public function deleteAd(AdsRepository $adRepository, $id, EntityManagerInterface $entityManager)
    {
        $ad = $adRepository->findOneBy(["id" => $id]);
        $entityManager->remove($ad);
        $entityManager->flush();

        return $this->redirectToRoute('ads');
    }

    #[Route('/adStatus/{id}', name: 'changeStatus', methods: ['GET'])]
    public function changeStatus(AdsRepository $adRepository, $id, EntityManagerInterface $entityManager)
    {
        $ad = $adRepository->findOneBy(["id" => $id]);

        if ($ad->getDone() == false) {
            $ad->setDone(true);
        } else {
            $ad->setDone(false);
        }
        $entityManager->persist($ad);
        $entityManager->flush();

        return $this->render('ads/deleteAd.html.twig', [
            'ad' => $ad
        ]);

    }

}
