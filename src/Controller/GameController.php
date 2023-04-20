<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\CardGame\CardGame;
use App\Card\DeckOfCards;
use App\Player\Dealer;
use App\Player\Player;

class GameController extends AbstractController {
    #[Route('/game/game', 'card_game')]
    public function game(SessionInterface $session): Response {
        $gameSession = $session->get('game') ?? null;
        if (is_null($gameSession)) {
            $gameSession = new CardGame(new Dealer('Dealer'), new Player('Player'), new DeckOfCards());
        }

        $session->set('game', $gameSession);
        
        if ($gameSession->isGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $data = [
            'gameSession' => $gameSession,
            'currentPlayerCards' => $gameSession->getCurrentPlayer()->getCards()
        ];
        
        return $this->render('game.html.twig', $data);
    }

    #[Route('/game/restart', 'restart_game')]
    public function restartGame(SessionInterface $session): Response {
        $gameSession = $session->get('game') ?? null;
        if ($gameSession instanceof CardGame) {
            $session->remove('game');
        }

        return $this->redirectToRoute('pre_game');
    }

    #[Route('/game/pre', 'pre_game')]
    public function preGame(): Response {
        return $this->render('pre_game.html.twig');
    }

    #[Route('/game/draw', 'draw_card_game')]
    public function drawCard(SessionInterface $session): Response {
        $gameSession = $session->get('game') ?? null;
        if (!$gameSession instanceof CardGame) {
            return $this->redirectToRoute('pre_game');
        }

        if ($gameSession->getCurrentPlayer() instanceof Dealer) {
            $gameSession->dealerDrawCards();
            return $this->redirectToRoute('card_game', ['gameSession' => $gameSession]);
        }

        $gameSession->currentPlayerDrawCard();

        $session->set('game', $gameSession);

        return $this->redirectToRoute('card_game', ['gameSession' => $gameSession]);
    }

    #[Route('/game/stop-player', 'stop_player_card_game')]
    public function stopGame(SessionInterface $session): Response {
        $gameSession = $session->get('game') ?? null;
        if (!$gameSession instanceof CardGame) {
            return $this->redirectToRoute('pre_game');
        }

        $gameSession->stopCurrentPlayer();
        $gameSession->nextPlayer();

        if ($gameSession->getCurrentPlayer() instanceof Dealer) {
            $gameSession->dealerDrawCards();
            return $this->redirectToRoute('card_game', ['gameSession' => $gameSession]);
        }

        $session->set('game', $gameSession);

        return $this->redirectToRoute('card_game', ['gameSession' => $gameSession]);
    }

    #[Route('/game/over', 'game_over')]
    public function gameOver(SessionInterface $session): Response {
        $gameSession = $session->get('game') ?? null;
        if (!$gameSession instanceof CardGame) {
            return $this->redirectToRoute('pre_game');
        }

        $data = [
            'gameSession' => $gameSession,
            'winner' => $gameSession->getWinner(),
            'loser' => $gameSession->getLoser()
        ];

        $session->remove('game');
        return $this->render('game_over.html.twig', $data);
    }
}