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

class PokerController extends AbstractController
{
    #[Route('/proj/game', 'proj/game')]
    public function poker(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame') ?? null;
        if (is_null($pokerGame)) {
            $pokerGame = new PokerGame(new Dealer('Dealer', money: 100 ), new Player('Player', money: 100), new DeckOfCards());
        }

        if ($pokerGame->isGameOver()) {
            return $this->redirectToRoute('proj/game/game_over');
        }

        $session->set('pokerGame', $pokerGame);

        return $this->render('proj/game.html.twig', ['pokerGame' => $pokerGame]);
    }

    #[Route('/proj/game/bet', 'proj/game/bet')]
    public function bet(SessionInterface $session, Request $req): Response
    {
        $amount = $req->query->get('bet') ?? null;
        if (is_null($amount) || $amount <= 0) {
            throw new Exception('No bet amount');
        }

        $pokerGame = $session->get('pokerGame');

        $pokerGame->currentPlayerBet($amount);
        $session->set('pokerGame', $pokerGame);

        return $this->redirectToRoute('proj/game');
    }
    
    #[Route('/proj/game/check', 'proj/game/check')]
    public function check(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');

        if ($pokerGame->getCurrentBet() === 0) {
            throw new Exception('No Current bet');
        }

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

    #[Route('/proj/game/game_over', 'proj/game/game_over')]
    public function gameOver(SessionInterface $session): Response
    {
        $pokerGame = $session->get('pokerGame');
        $session->remove('pokerGame');

        $data = [
            'gameSession' => $pokerGame,
            'winner' => $pokerGame->getWinner(),
            'loser' => $pokerGame->getLoser(),
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