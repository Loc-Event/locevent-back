<?php

namespace App\Controller\Api\v1;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', name: 'api_')]
class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $listOfUsers = $userRepository->findAll();
        // dd($listOfUsers);

        return $this->json($listOfUsers, 200);
    }
}
