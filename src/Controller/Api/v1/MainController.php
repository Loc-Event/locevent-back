<?php

namespace App\Controller\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/api/v1/', name: 'app_api_v1_index')]
    public function index(): Response
    {
        return $this->render('api/v1/main/index.html.twig');
    }
}
