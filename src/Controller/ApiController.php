<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    // METHODE 1 POUR ENTITE REGIONS ***************************
    #[Route('/api/region', name: 'api')]
    public function addRegionByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
            // Recuperation des Regions en Json
            $regionJson = file_get_contents("https://geo.api.gouv.fr/regions");

/*
    // Method 1
            // Decode Json to Array
            $regionTab = $serializer->decode($regionJson,"json");

            // Denormalize Array to Object
            $regionObject = $serializer->denormalize($regionTab, "App\Entity\Region[]");
*/


    // Method 2 
            // Deserialize JSON to Object
            $regionObject = $serializer->deserialize($regionJson,'App\Entity\Region[]','json');

             foreach ($regionObject as $region) {
                 $em->persist($region);
             }
            $em->flush();


        return new JsonResponse("success", Response::HTTP_CREATED, [], true);
    }


    #[Route('/api/show_regions', name: 'get_regions_api_BD')]
    public function showRegion(SerializerInterface $serializer, RegionRepository $regionRepository, ValidatorInterface $validator)
    {
        // Récuperer tous les régions dans la base de données
        $regionsObject=$regionRepository->findAll();

        // Serialize Object to Json
        $regionsJson =$serializer->serialize($regionsObject, "json");

        return new JsonResponse($regionsJson, Response::HTTP_OK,[],true);
    }

    #[Route('/api/post_regions', name: 'post_regions_api_BD')]
    public function addRegion(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator){
        // Récupérer le contenu du bout
        $regionJson = $request->getContent();
        dd($regionJson);
    }


    // POUR ENTITE DEPARTEMENT *******************************
    #[Route('/api/departement', name: 'api')]
    public function addDepartement(Request $request,ValidatorInterface $validator,
    SerializerInterface $serializer,RegionRepository $repo)
    {
        //Recuperation du Contenu Json
        $departementJson = $request->getContent();

        //Transformation du contenu en Tableau
        $departementTable=$serializer->decode($departementJson,"json" );

        //Recuperation de l'objet Region
        $region =$repo->find((int)$departementTable["region"]["id"]);
        $departementsObject=$serializer->deserialize($request->getContent(), Departement::class,'json');
        $departementsObject->setRegion($region);
        $errors = $validator->validate($departementsObject);
        
        if (count($errors) > 0) {
        $errorsString =$serializer->serialize($errors,"json");
        return new JsonResponse( $errorsString ,Response::HTTP_BAD_REQUEST,[],true);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($departementsObject);
        $entityManager->flush();
        return new JsonResponse("succes",Response::HTTP_CREATED,[],true);
    }






    // POUR ENTITE COMMUNE *************************************
    #[Route('/api/commune', name: 'api')]
    public function addcommuneByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        // Recuperation des Départements en Json
        $communeJson=file_get_contents("https://geo.api.gouv.fr/communes");

        //  Decode Json en Array
        $communeTab=$serializer->decode($communeJson, "json");

        // Démoralise Array to Object
        $communeObject = $serializer->denormalize($communeTab, 'App\Entity\Commune[]');


        dd($communeJson);
        return $this->json([
            'message' => 'Welcom to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }



}
