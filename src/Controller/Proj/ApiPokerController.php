<?php
namespace App\Controller\Proj;

use App\Card\DeckOfCards;
use App\PokerGame\PokerGame;
use App\Player\Dealer;
use App\Player\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiPokerController {
    #[Route('/proj/game/api/changeCards', 'proj/game/api/changeCards', methods: ['POST'])]
    public function changeCards(SessionInterface $session, Request $req): JsonResponse
    {
        $cardIndices = json_decode($req->getContent());
        $pokerGame = $session->get('pokerGame');

        try {
            $pokerGame->currentPlayerChangeCards($cardIndices);
            $session->set('pokerGame', $pokerGame);
            return new JsonResponse($pokerGame->getCurrentPlayerCards());
        } catch(Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }
    }

    #[Route('/proj/game/api/current-round', 'proj/game/api/current-round', methods: ['GET'])]
    public function getCurrentRound(SessionInterface $session, Request $req): JsonResponse
    {
        $pokerGame = $session->get('pokerGame');

        return new JsonResponse($pokerGame->getCurrentRound());
    }
}