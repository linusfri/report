<?php

namespace App\Controller\Proj;

use App\Card\DeckOfCards;
use App\Player\Player;
use App\Player\PokerDealer;
use App\PokerGame\PokerGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PokerController extends AbstractController
{
    #[Route('/proj/game', 'proj/game')]
    public function poker(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame') ?? null;
        if (is_null($pokerGame)) {
            $pokerGame = new PokerGame(new PokerDealer('Dealer', money: 200), new Player('Player', money: 100), new DeckOfCards());
        }

        if ($pokerGame->isGameOver()) {
            return $this->redirectToRoute('proj/game/game_over');
        }

        /* Do this to simulate dealer turn */
        if ($pokerGame->getCurrentPlayer()->getId() === $pokerGame->dealer->getId()) {
            $pokerGame->dealerEmulateTurn();

            if ($pokerGame->isGameOver()) {
                return $this->redirectToRoute('proj/game/game_over');
            }
        }

        $session->set('pokerGame', $pokerGame);

        return $this->render('proj/game.html.twig', ['pokerGame' => $pokerGame]);
    }

    #[Route('/proj/game/raise', 'proj/game/raise')]
    public function bet(SessionInterface $session, Request $req): Response
    {
        $amount = $req->query->get('bet') ?? null;

        $pokerGame = $session->get('pokerGame');

        $pokerGame->currentPlayerRaise($amount);
        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game');
    }

    #[Route('/proj/game/check', 'proj/game/check')]
    public function check(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');

        $pokerGame->currentPlayerCheck();

        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game');
    }

    #[Route('/proj/game/fold', 'proj/game/fold')]
    public function fold(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');
        $pokerGame->currentPlayerFold();

        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game');
    }

    #[Route('/proj/game/call', 'proj/game/call')]
    public function call(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');
        $pokerGame->currentPlayerCall();

        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game');
    }

    #[Route('proj/game/done-change', 'proj/game/done-change')]
    public function doneChange(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');
        $pokerGame->currentPlayerDoneChangeCards();

        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game');
    }

    #[Route('/proj/game/showdown', 'proj/game/showdown')]
    public function showdown(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');

        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game/game_over');
    }

    #[Route('/proj/game/game_over', 'proj/game/game_over')]
    public function gameOver(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');
        $session->remove('pokerGame');

        $data = [
            'pokerGame' => $pokerGame,
            'winner' => $pokerGame->getPokerWinner(),
            'loser' => $pokerGame->getPokerLoser(),
        ];

        return $this->render('proj/game_over.html.twig', $data);
    }

    #[Route('/proj/reset', 'proj/reset')]
    public function resetSession(SessionInterface $session): Response
    {
        $session->remove('pokerGame');

        return $this->render('proj/home.html.twig');
    }
}
