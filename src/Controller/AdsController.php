<?php

namespace App\Controller;
// liens vers fichiers ou composants symfony dont on aura besoin
use App\Entity\Ads;
use App\Entity\ProfilePics;
use App\Entity\User;
use App\Entity\Media;
use App\Form\AdTypeForm;
use App\Form\UserType;
use App\Repository\AdsRepository;
use App\Repository\AdTypeRepository;
use App\Repository\BrandRepository;
use App\Repository\MaterialTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdsController extends AbstractController // classe référente l'entité Ads
{
    #[Route('/ads', name: 'ads', methods: ['GET'])]
    // lien / route vers url concernée par la fonction
    public function ads(
        AdsRepository $adsRepository,
        AdTypeRepository $adTypeRepository,
        BrandRepository $brandRepository,
        MaterialTypeRepository $materialTypeRepository
        ): Response
        // les paramètres nécessaires à ma fonction - modèle de référence auquel
        // on attribut une variable
    {
        $ads = $adsRepository->findBy(['done' => false], ['date' => 'DESC']);
        $adTypes = $adTypeRepository->findAll();
        $brands = $brandRepository->findAll();
        $materialTypes = $materialTypeRepository->findAll();
        // création de varibles => requêtes sur les variables de modèles
        return $this->render('ads/ads.html.twig', [
            'ads' => $ads,
            'adTypes' => $adTypes,
            'brands' => $brands,
            'materialTypes' => $materialTypes
        ]);
        // renvoi à la vue url et ce qu'on va y afficher

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
        // création de nouveaux objets
        $author = $this->getUser();
        // importation de données
        $now = new \DateTime('now');
        // variable de création de date courante

        $form = $this->createForm(AdTypeForm::class, $ad);
        // création d'un formulaire de type "AdTypeForm" qui sera $ad
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->get('media')->getData();
            //récupération des données du champ media
            if($mediaFile) {
                // si photo, on la traite et on l'ajoute avec sluggerInterface
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // on récupère les infos originales
                $safeFilename = $slugger->slug($originalFilename);
                // on sécurise le nom de fichier
                $newFilename = $safeFilename. '-' .uniqid().'.'.$mediaFile->guessExtension();
                // on lui applique un nom unique
                $mediaFile->move(
                    $this->getParameter('pathUpload_directory'), $newFilename
                    // on l'envoie vers notre fichier media
                );
                $media->setName($newFilename);
                // on attibut à notre variable $media la valeur de $newFilename
                $ad->setMedia($media);
                // on ajoute la photo à l'objet $ad
            }
            // si formulaire est soumis et valide, 
            $ad->setDate($now);
            $ad->setDone(false);
            $ad->setAuthor($author);
            // on lui ajoute la date du jour, on l'initie à false et on ajoute l'auteur
            $adRepository->add($ad);
            // création de l'objet $ad

            return $this->redirectToRoute('ads');
            // renvoi vers url ads
        }
        return $this->render('ads/addAd.html.twig', ['adForm' => $form->createView()]);
        //affichage du formulaire dans url ads
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
        // flush pour effacer les données en base mySql

        return $this->redirectToRoute('ads');
    }

    #[Route('/adStatus/{id}', name: 'changeStatus', methods: ['GET'])]
    public function changeStatus(AdsRepository $adRepository, $id, EntityManagerInterface $entityManager)
    {
        $ad = $adRepository->findOneBy(["id" => $id]);

        if ($ad->getDone() == false) {
            $ad->setDone(true);
            $entityManager->persist($ad);
            $entityManager->flush();

            return $this->render('ads/deleteAd.html.twig', [
                'ad' => $ad
            ]);
        }
        else {
            $ad->setDone(false);
            $entityManager->persist($ad);
            $entityManager->flush();

            return $this->redirectToRoute('ads', [
                'ad' => $ad
            ]);
        }

    }
    #[Route('/adsByTypes', name: 'adsByTypes', methods: ['GET'])]
    public function adsByTypes (
        Request $request,
        AdsRepository $adsRepository,
        AdTypeRepository $adTypeRepository,
        BrandRepository $brandRepository,
        MaterialTypeRepository $materialTypeRepository
        )
    {
        $title = $request->query->get('title');
        $adType = $request->query->get('adType');
        $brand = $request->query->get('brand');
        $materialType = $request->query->get('materialType');
        // on instantie des variables à travers lesquelles on va récupérer des données
        $ads = $adsRepository->getAdsByTypes($title, $adType, $brand, $materialType);
        // on appelle une fonction DQL sur le modèle Ads avec en paramètres nos variables
        $adTypes = $adTypeRepository->findAll();
        $brands = $brandRepository->findAll();
        $materialTypes = $materialTypeRepository->findAll();
        // on requête sur les modèles correspondants à nos variables
        return $this->render('ads/ads.html.twig', [
            'ads' => $ads,
            'adTypes' => $adTypes,
            'brands' => $brands,
            'materialTypes' => $materialTypes
        ]);
        // on renvoi à la vue nos données
    }
    #[Route('/profile', name: 'profile', methods: ['GET', 'POST'])]
    public function index(AdsRepository $adsRepository)
    {
        $ads = $adsRepository->findBy(['done' => false], ['date' => 'DESC']);

        return $this->render('ads/profile.html.twig', [
            'ads' => $ads,
        ]);
    }
    #[Route('/updateUser/{id}', name: 'updateUser', methods: ['GET', 'POST'])]
    public function updateUser (
        $id,
        UserRepository $userRepository,
        Request $request,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $media = new ProfilePics();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('profilePics')->getData()) {
                $mediaFile = $form->get('profilePics')->getData();
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $fileName = $safeFilename . '-' . uniqid() . '-' . $mediaFile->guessExtension();
                $mediaFile->move($this->getParameter('pathUpload_directory'), $fileName);

                $media->setName($fileName);
                $user->setProfilePics($media);
            } 
            $userRepository->add($user);
            return $this->redirectToRoute('profile');
        }
        return $this->render('ads/updateUser.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }

}
