<?php

namespace App\Controller\Proj;

use App\Card\DeckOfCards;
use App\Player\Player;
use App\Player\PokerDealer;
use App\PokerGame\PokerGame;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiPokerController
{
    #[Route('/proj/game/api/changeCards', 'proj/game/api/changeCards', methods: ['POST'])]
    public function changeCards(SessionInterface $session, Request $req): JsonResponse
    {
        $cardIndices = json_decode($req->getContent());
        $pokerGame = $session->get('pokerGame') ?? null;

        try {
            $pokerGame->currentPlayerChangeCards($cardIndices);
            $session->set('pokerGame', $pokerGame);

            return new JsonResponse($pokerGame->getCurrentPlayerCards());
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }
    }

    #[Route('/proj/game/api/current-round', 'proj/game/api/current-round', methods: ['GET'])]
    public function getCurrentRound(SessionInterface $session): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        return new JsonResponse($pokerGame->getCurrentRound());
    }

    #[Route('proj/game/api/previous-action', 'proj/game/api/previous-action', methods: ['GET'])]
    public function getPreviousAction(SessionInterface $session): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        $lastAction = $pokerGame->dealer->getPreviousAction();

        return new JsonResponse($lastAction);
    }

    #[Route('proj/game/api/get-player-name', 'proj/game/api/get-player-name', methods: ['GET'])]
    public function getPlayerName(SessionInterface $session): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        $name = $pokerGame->player->getName();

        return new JsonResponse($name);
    }

    #[Route('proj/game/api/get-dealer-name', 'proj/game/api/get-dealer-name', methods: ['GET'])]
    public function getDealerName(SessionInterface $session): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        $name = $pokerGame->dealer->getName();

        return new JsonResponse($name);
    }

    #[Route('proj/game/api/get-current-player', 'proj/game/api/get-current-player', methods: ['GET'])]
    public function getCurrentPlayer(SessionInterface $session): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        $currentPlayer = $pokerGame->getCurrentPlayer();

        return new JsonResponse($currentPlayer);
    }

    #[Route('proj/game/api/get-current-opponent', 'proj/game/api/get-current-opponent', methods: ['GET'])]
    public function getCurrentOpponent(SessionInterface $session): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        $opponent = $pokerGame->getCurrentOpponent();

        return new JsonResponse($opponent);
    }

    #[Route('proj/game/api/set-current-player-money', 'proj/game/api/set-current-player-money', methods: ['POST'])]
    public function setCurrentPlayerMoney(SessionInterface $session, Request $req): JsonResponse
    {
        $pokerGame = $session->get('pokerGame') ?? null;
        $amount = $req->request->get('money');

        if (!is_numeric($amount)) {
            return new JsonResponse('Amount must be a number', 500);
        }

        if (!$pokerGame) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        }

        $pokerGame->getCurrentPlayer()->setMoney((int) $amount);
        $session->set('pokerGame', $pokerGame);
        return new JsonResponse(['current_player_money' => $pokerGame->getCurrentPlayer()->getMoney()]);
    }
}
