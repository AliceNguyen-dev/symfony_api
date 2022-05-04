<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
// use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    // METHODE 1
    #[Route('/api/regions', name: 'api')]
    public function addRegionByApi(SerializerInterface $serializer): Response
    {
        // Recuperation des Régions en Json
        $regionJson=file_get_contents("https://geo.api.gouv.fr/regions");
        //  Decode Json en Array
        $regionTab=$serializer->decode($regionJson, "json");
        // Démoralise Array to Object
        $regionObject = $serializer->denormalize($regionTab, 'App\Entity\Region[]');

        // METHODE 2
        // deserialize JSON to Object
        // $regionObject = $serializer->deserialize($regionJson, 'App\Entity\Region[]', 'json');
        // dd($regionObject);

        // foreach ($regionObject as $region) {
        //     $em->persist($region);
        // }

        // $em->flush();

        // return new JsonResponse("sucess", Response::HTTP_CREATED, [], true);


        dd($regionJson);
        return $this->json([
            'message' => 'Welcom to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/show_regions', name: 'get_regions_api_BD')]
    public function showRegion(SerializerInterface $serializer, RegionRepository $regionRepository)
    {
        // Récuperer tous les régions dans la base de données
        $regionsObject=$regionRepository->findAll();

        // Serialize Object to Json
        $regionsJson =$serializer->serialize($regionsObject, "json");

        return new JsonResponse($regionsJson, Response::HTTP_OK,[],true);
    }

    #[Route('/api/post_regions', name: 'post_regions_api_BD')]
    public function addRegion(Request $request, SerializerInterface $serializer, EntityManagerInterface $em){
        // Récupérer le contenu du bout
        $regionJson = $request->getContent();
        dd($regionJson);
    }
}
