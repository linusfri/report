<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api')]
    public function api(): Response
    {
        $data = [
            'routeUrls' => [
                'api/quote',
                'api/deck',
                'api/deck/shuffle',
                'api/deck/draw',
                'api/deck/draw/5',
                'api/game',
                'api/library',
                'api/library/book/9780451524935',
            ],
        ];

        return $this->render('api.html.twig', $data);
    }
}
