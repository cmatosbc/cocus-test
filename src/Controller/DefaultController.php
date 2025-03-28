<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/{vueRouting}', requirements: ['vueRouting' => '^(?!api).+'], defaults: ['vueRouting' => null])]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/api', name: 'api_index', methods: ['GET'])]
    public function apiIndex(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'API is running',
            'version' => '1.0.0'
        ]);
    }
}
