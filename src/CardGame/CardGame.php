<?php

namespace App\CardGame;

use App\Player\PlayerInterface;
use App\Player\Dealer;
use App\Card\DeckOfCards;

class CardGame {
    private PlayerInterface $currentPlayer;

    /** $players
     * @var array<PlayerInterface> 
     * */
    private array $players;
    
    private int $playerRound;
    private bool $gameOver;
    private DeckOfCards $deck;

    /**
     * __construct
     *
     * @param array<PlayerInterface> $players
     */
    public function __construct(array $players, DeckOfCards $deck) {
        $this->start($players, $deck);
    }

    private function dealerExists(): bool {
        foreach ($this->players as $player) {
            if ($player instanceof Dealer) {
                return true;
            }
        }

        return false;
    }

    private function dealerExistsOnce(): bool {
        $results = array_filter($this->players, function($player) {
            return $player instanceof Dealer;
        });

        return count($results) === 1;
    }

    /**
     * Starts game
     *
     * @param array<PlayerInterface> $players
     * @return boolean
     */
    public function start(array $players, DeckOfCards $deck): bool {
        
        $this->playerRound = 0;
        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleCards();
        
        $this->players = array_merge([], $players);
        $this->currentPlayer = $this->setCurrentPlayer(0);
        
        if (count($players) > 2) {
            throw new \Exception('Currently only 2 players are supported.');
        }
        
        if (!$this->dealerExists()) {
            throw new \Exception('Dealer is required.');
        }
        
        if (!$this->dealerExistsOnce()) {
            throw new \Exception('There can only be one dealer.');
        }

        if (!count($players)) {
            throw new \Exception('No players provided.');

        }

        return true;
    }

    public function currentPlayerDrawCard(): void {
        $this->currentPlayer->drawCard($this->deck);
        
        $this->updateCurrentPlayer();

        $this->playerRound += 1;
    }

    public function dealerDrawCards(): void {
        if ($this->currentPlayer instanceof Dealer) {
            for ($i = 0; $i < 4; $i++) {
                if ($this->randomChance()) {
                    $this->playerRound += 1;
                    $this->currentPlayer->drawCard($this->deck);
                }

                if ($this->currentPlayer->getHandValue() >= 21) {
                    break;
                }
            }
        }

        $this->updateCurrentPlayer();
        return;
    }

    private function randomChance() {
        /** 50% chance */
        if (rand(0, 100) > 50) {
            return true;
        }

        return false;
    }

    public function getCurrentPlayerCards(): void {
        $this->currentPlayer->getCards;
    }
    
    public function updateCurrentPlayer(): void {
        $this->currentPlayer->countHandValue();
        
        if ($this->currentPlayer->getHandValue() >= 21) {
            $this->currentPlayer->setIsFinished(true);
            $this->nextPlayer();
        }
    }

    public function stopCurrentPlayer(): void {
        $this->currentPlayer->setIsFinished(true);
    }

    public function isCurrentPlayerFinished(): bool {
        return $this->currentPlayer->getIsFinished();
    }

    public function checkAllPlayersFinished(): bool {
        foreach ($this->players as $player) {
            if (!$player->getIsFinished()) {
                return false;
            }
        }

        return true;
    }

    public function nextPlayer(): void {
        if ($this->checkAllPlayersFinished()) return;

        $curIndex = $this->getIndexOfPlayer($this->currentPlayer);
        $nextIndex = ($curIndex + 1) % count($this->players);

        if ($this->players[$nextIndex]->getIsFinished()) {
            $this->currentPlayer = $this->players[$nextIndex];
            $this->nextPlayer();

            return;
        }

        $this->currentPlayer = $this->players[$nextIndex];
        $this->playerRound = 0;

        return;
    }

    public function getIndexOfPlayer(PlayerInterface $player): int|false {
        for ($i = 0; $i < count($this->players); $i++) {
            if ($this->players[$i]->getId() === $player->getId()) {
                return $i;
            }
        }

        return false;
    }

    public function setCurrentPlayer(int $index): PlayerInterface {
        return $this->players[$index];
    }

    public function getCurrentPlayer(): PlayerInterface {
        return $this->currentPlayer;
    }

    public function setGameOver(bool $value = true): void {
        $this->gameOver = $value;
    }

    public function isGameOver(): bool {
        return $this->gameOver;
    }

    public function updateGameState(): void {
        $this->updateCurrentPlayer();

        if ($this->checkAllPlayersFinished()) {
            $this->setGameOver(true);
        }
    }

    public function getCurrentPlayerRound(): int {
        return $this->playerRound;
    }

    public function getWinner(): PlayerInterface {
        // TODO: Implement for more than 2 players
        $dealer = array_values(array_filter($this->players, function($player) {
            return $player instanceof Dealer;
        }))[0];

        if ($dealer->getHandValue() === 21) {
            return $dealer;
        }

        $playerWinner = null;

        foreach($this->players as $player) {
            if ($player instanceof Dealer) continue;

            if ($player->getHandValue() > $dealer->getHandValue() && $player->getHandValue() <= 21) {
                $playerWinner = $player;
            } else if ($dealer->getHandValue() > 21 && $player->getHandValue() <= 21) {
                $playerWinner = $player;
            }
        }

        if (is_null($playerWinner)) {
            return $dealer;
        }

        return $playerWinner;
    }

    public function reset(): void {
        $this->setGameOver(false);
        $this->resetPlayers($this->players);

        $this->start($this->players, new DeckOfCards());
    }

    /**
     * Resets players scores and card hands
     *
     * @param array<PlayerInterface> $players
     * @return void
     */
    public function resetPlayers(array $players): void {
        $numberOfPlayers = count($players);
        for ($i = 0; $i < $numberOfPlayers; $i++) {
            $players[$i]->reset();
        }
    }
}