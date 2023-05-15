<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiGameController extends AbstractController
{
    #[Route('/api/game', 'api_game')]
    public function apiGame(SessionInterface $session): JsonResponse
    {
        $gameSession = $session->get('game') ?? 'No game started yet!';

        return new JsonResponse(['game' => $gameSession]);
    }
}
