<?php

namespace App\Controller\Api\v1;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/user', name: 'api_user_')]
class UserController extends AbstractController
{
    // #[Route('/', name: 'index', methods: ['GET'])]
    // public function index(UserRepository $userRepository): JsonResponse
    // {
    //     // Création requête pour récupérer l'utilisateur avec rôle admin
    //     $listUsers = $userRepository->findByRole('[]');

    //     return $this->json($listUsers, 200, [], [
    //         "groups" => "user_list"
    //     ]);
    // }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, 200, [], [
            "groups" => "user_show"
        ]);
    }
        
    #[Route('/{id}', name: 'edit', methods: ['PATCH', 'PUT'])]
    public function edit(User $user, Request $request, SerializerInterface $serialiser, EntityManagerInterface $entityManagerInterface): JsonResponse
    {   
        // Récupère les données json envoyées par le front
        $jsonData = $request->getContent();

        // Serialize, transforme l'objet JSON en objet PHP
        $data = json_decode($jsonData);

        // We transform the JSON on object of entity User and merge those informations in the $user
        $serialiser->deserialize($jsonData, User::class, 'json', [AbstractNormalizer ::OBJECT_TO_POPULATE => $user]);

        $entityManagerInterface->flush();
        
        // dd($user);

        return $this->json($user, 200, [], [
            "groups" => "user_edit"
        ]);
    }
}
