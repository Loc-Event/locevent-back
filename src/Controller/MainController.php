<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $this->addFlash("success", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem architecto fuga voluptatem illo nisi natus, veniam molestiae ");
        // $this->addFlash("danger", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem architecto fuga voluptatem illo nisi natus, veniam molestiae ");
        // $this->addFlash("warning", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem architecto fuga voluptatem illo nisi natus, veniam molestiae ");
        // $this->addFlash("info", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Rem architecto fuga voluptatem illo nisi natus, veniam molestiae ");


        return $this->render('main/index.html.twig');
    }
}
