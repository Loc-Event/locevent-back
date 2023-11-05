<?php

namespace App\Controller\Api\v1;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/v1/user', name: 'api_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        // Création requête pour récupérer l'utilisateur avec rôle admin
        $listUsers = $userRepository->findByRole('[]');

        return $this->json($listUsers, 200, [], [
            "groups" => "user_show"
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, 200, [], [
            "groups" => "user_show"
        ]);
    }
}
