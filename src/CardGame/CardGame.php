<?php

namespace App\CardGame;

use App\Player\PlayerInterface;
use App\Player\Dealer;
use App\Player\Player;
use App\Card\DeckOfCards;

class CardGame {
    private PlayerInterface $currentPlayer;
    private Player $player;
    private Dealer $dealer; 
    private int $playerRound;
    private bool $gameOver;
    private DeckOfCards $deck;

    /**
     * __construct
     *
     * @param array<PlayerInterface> $players
     */
    public function __construct(Dealer $dealer, Player $player, DeckOfCards $deck) {
        $this->start($dealer, $player, $deck);
    }

    /**
     * Starts game
     *
     * @param array<PlayerInterface> $players
     * @return boolean
     */
    public function start(Dealer $dealer, Player $player, DeckOfCards $deck): bool {
        
        $this->player = $player;
        $this->dealer = $dealer;
        $this->playerRound = 0;
        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleCards();
        
        $this->currentPlayer = $this->player;

        return true;
    }

    public function currentPlayerDrawCard(): void {
        $this->currentPlayer->drawCard($this->deck);
        
        $this->updateGameState();

        $this->playerRound += 1;
    }

    public function dealerDrawCards(): void {
        $this->currentPlayer = $this->dealer;

        for ($i = 0; $i < 4; $i++) {
            if ($this->randomChance()) {
                $this->playerRound += 1;
                $this->currentPlayer->drawCard($this->deck);
            }

            if ($this->currentPlayer->getHandValue() >= 21) {
                break;
            }
        }

        $this->updateGameState();
        return;
    }

    private function randomChance() {
        /** 50% chance */
        if (rand(0, 100) > 50) {
            return true;
        }

        return false;
    }

    public function getCurrentPlayerCards(): array {
        return $this->currentPlayer->getCards();
    }
    
    public function updateCurrentPlayer(): void {
        $this->currentPlayer->countHandValue();
        
        if ($this->currentPlayer->getHandValue() >= 21) {
            $this->currentPlayer->setIsFinished(true);
        }
    }

    public function updateDealer(): void {
        $this->currentPlayer = $this->dealer;
        $this->currentPlayer->countHandValue();
        
        $this->currentPlayer->setIsFinished(true);
    }

    public function stopCurrentPlayer(): void {
        $this->currentPlayer->setIsFinished(true);

        $this->updateGameState();
    }

    public function isCurrentPlayerFinished(): bool {
        return $this->currentPlayer->getIsFinished();
    }

    public function checkAllPlayersFinished(): bool {
        return $this->dealer->getIsFinished() && $this->player->getIsFinished();
    }

    public function nextPlayer(): void {
        if ($this->checkAllPlayersFinished()) return;

        $this->currentPlayer = $this->currentPlayer instanceof Player ? $this->dealer : $this->player;
        $this->playerRound = 0;

        $this->updateGameState();

        return;
    }

    public function getCurrentPlayer(): PlayerInterface {
        return $this->currentPlayer;
    }

    public function setCurrentPlayerScore() {
        $this->currentPlayer->setHandValue(21);
    }

    public function setGameOver(bool $value = true): void {
        $this->gameOver = $value;
    }

    public function isGameOver(): bool {
        return $this->gameOver;
    }

    public function updateGameState(): void {
        if ($this->currentPlayer instanceof Dealer) {
            $this->updateDealer();
        } else {
            $this->updateCurrentPlayer();
        }

        if ($this->currentPlayer->getHandValue() > 21) {
            $this->setGameOver(true);
            return;
        } 
        
        if ($this->checkAllPlayersFinished()) {
            $this->setGameOver(true);
            return;
        } 
        
        if (
            $this->currentPlayer->getHandValue() === 21 && $this->currentPlayer instanceof Player
        ) {
            $this->dealerDrawCards();
            return;
        }
    }

    public function getCurrentPlayerRound(): int {
        return $this->playerRound;
    }

    public function getWinner(): PlayerInterface {
        // TODO: Implement for more than 2 players
        if (
            $this->dealer->getHandValue() > 21 && $this->player->getHandValue() > 21
        ) {
            throw new \Exception('Both players can not have more than 21 points.');
        }

        if ($this->dealer->getHandValue() === 21) {
            return $this->dealer;
        }

        if (
            $this->dealer->getHandValue() > 21 && $this->player->getHandValue() <= 21
        ) {
            return $this->player;
        }

        if (
            ($this->player->getHandValue() > 21 && $this->dealer->getHandValue() <= 21)
            ||
            $this->player->getHandValue() === $this->dealer->getHandValue()
        ) {
            return $this->dealer;
        }

        if (
            $this->player->getHandValue() > $this->dealer->getHandValue() && $this->player->getHandValue() <= 21
        ) {
            return $this->player;
        }

        return $this->dealer;
    }

    public function getLoser(): PlayerInterface {
        $loser = $this->getWinner()->getId() !== $this->dealer->getId() ? $this->dealer : $this->player;

        return $loser;
    }

    public function reset(): void {
        $this->setGameOver(false);
        $this->dealer->reset();
        $this->player->reset();

        $this->start($this->player, $this->dealer, new DeckOfCards());
    }
}