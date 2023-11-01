<?php

namespace App\Controller\Api\v1;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/v1/test', name: 'app_api_v1_test')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $listOfUsers = $userRepository->findAll();
        // dd($listOfUsers);

        return $this->json($listOfUsers, 200);
    }
}
