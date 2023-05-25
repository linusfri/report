<?php

namespace App\PokerGame;

use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Player\Dealer;
use App\Player\Player;
use App\Player\PlayerInterface;
use Exception;

class PokerGame implements \JsonSerializable
{
    protected PlayerInterface $currentPlayer;
    public Player $player;
    public Dealer $dealer;
    protected int $round;
    protected bool $gameOver;
    protected DeckOfCards $deck;
    protected int $currentBet;
    protected int $totalPot;
    protected bool $changeCardRound;

    public function __construct(Dealer $dealer, Player $player, DeckOfCards $deck)
    {
        $this->start($dealer, $player, $deck);
    }

    public function start(Dealer $dealer, Player $player, DeckOfCards $deck): bool
    {
        $this->player = $player;
        $this->dealer = $dealer;
        $this->round = 1;
        $this->changeCardRound = false;
        $this->currentBet = 10; // Simulate ante per player
        $this->totalPot = 0;
        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleCards();

        $this->currentPlayer = $this->player;

        $this->initialBet();
        $this->dealCards();

        return true;
    }

    protected function initialBet(): void {
        $this->totalPot += $this->player->bet(10);
        $this->totalPot += $this->dealer->bet(10);
    }

    public function dealCards(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->player->drawCard($this->deck);
            $this->dealer->drawCard($this->deck);
        }

        $this->updateGameState();
    }

    public function currentPlayerFold(): void
    {
        $this->currentPlayer->fold();

        $this->currentPlayer->setHasPlayedRound();
        $this->nextPlayer();
        $this->updateGameState();
    }

    public function currentPlayerBet(int $amount): bool
    {
        try {
            /** 
             * Do this to ensure that pot only increments with what player actually can bet.
             * Check implementation of Player::bet() for more info.
             * */
            $bet = $this->currentPlayer->bet($amount);
    
            $this->totalPot += $bet;
    
            if ($bet >= $this->currentBet) {
                $this->setcurrentBet($bet);
                $this->nextPlayer();
            } else if($bet < $this->currentBet) {
                throw new Exception('Bet must be higher than current bet');
            }
        } catch (Exception $e) {
            return false;
        }

        $this->currentPlayer->setHasPlayedRound();
        $this->updateGameState();

        return true;
    }

    public function currentPlayerCall(): bool
    {
        try {
            $bet = $this->currentPlayer->bet($this->currentBet);
    
            $this->totalPot += $bet;
    
            if ($bet >= $this->currentBet) {
                $this->setcurrentBet($bet);
                $this->nextPlayer();
            } else if($bet < $this->currentBet) {
                throw new Exception('Call must be equal to current bet');
            }
        } catch (Exception $e) {
            return false;
        }

        $this->currentPlayer->setHasPlayedRound();
        $this->updateGameState();

        return true;
    }

    public function currentPlayerCheck(): bool
    {  
        $this->currentPlayer->setHasPlayedRound();
        $this->nextPlayer();
        $this->updateGameState();

        return true;
    }

    public function setcurrentBet(int $amount): void
    {
        if ($amount < $this->currentBet) {
            throw new Exception('Bet must be higher than current bet');
        }
        $this->currentBet = $amount;
    }

    public function getcurrentBet(): int
    {
        return $this->currentBet;
    }

    public function dealerDrawCards(int $number): void
    {
        /** If number bigger than three draw max allowed number of cards */
        if ($number > 3) {
            for ($i = 1; $i <= $number; $i++) {
                if ($this->randomChance()) {
                    $this->currentPlayer->drawCard($this->deck);
                }
            }

            $this->updateGameState();

            return;
        }

        /** Else draw specified number of cards */
        for ($i = 1; $i <= $number; ++$i) {
            if ($this->randomChance()) {
                $this->dealer->drawCard($this->deck);
            }
        }

        $this->updateGameState();

        return;
    }

    private function randomChance(): bool
    {
        /* 50% chance */
        if (rand(0, 100) > 50) {
            return true;
        }

        return false;
    }

    /**
     * getCurrentPlayerCards.
     *
     * @return array<CardGraphic>
     */
    public function getCurrentPlayerCards(): array
    {
        return $this->currentPlayer->getCards();
    }

    private function updatePlayer(): void
    {
        // TODO poker hands
    }

    private function updateDealer(): void
    {
        // TODO poker hands
    }

    public function stopCurrentPlayer(): void
    {
        $this->currentPlayer->setIsFinished();

        $this->updateGameState();
    }

    public function checkAllPlayersFinished(): bool
    {
        return $this->dealer->getIsFinished() && $this->player->getIsFinished();
    }

    public function nextPlayer(): void
    {
        if ($this->checkAllPlayersFinished()) {
            throw new Exception('All players are finished');
        }

        $this->currentPlayer = $this->getCurrentOpponent();

        $this->updateGameState();

        return;
    }

    public function getCurrentPlayer(): PlayerInterface
    {
        return $this->currentPlayer;
    }

    public function getCurrentOpponent(): PlayerInterface
    {
        return $this->currentPlayer->getId() !== $this->dealer->getId() ? $this->dealer : $this->player;
    }

    public function setGameOver(): void
    {
        $this->gameOver = true;
    }

    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    private function updateGameState(): void
    {
        /** This only works for two players, need to be changed for arbitrary number of players */
        if ($this->player->getIsFinished() || $this->dealer->getIsFinished()) {
            $this->setGameOver();
        }

        if ($this->player->getHasPlayedRound() && $this->dealer->getHasPlayedRound()) {
            $this->nextRound();
            $this->player->resetHasPlayedRound();
            $this->dealer->resetHasPlayedRound();
            $this->changeCardRound = true;
        }
    }

    public function getCurrentRound(): int
    {
        return $this->round;
    }

    public function nextRound(): void
    {
        ++$this->round;

        $this->currentPlayer = $this->player;
    }

    public function getWinner(): PlayerInterface
    {
        return $this->player->getIsFinished() ? $this->dealer : $this->player;
    }

    public function getLoser(): PlayerInterface
    {
        $loser = $this->getWinner()->getId() !== $this->dealer->getId() ? $this->dealer : $this->player;

        return $loser;
    }

    public function reset(): void
    {
        $this->gameOver = false;
        $this->dealer->reset();
        $this->player->reset();

        $this->start($this->dealer, $this->player, new DeckOfCards());
    }

    public function jsonSerialize(): mixed
    {
        return [
            'currentPlayer' => $this->currentPlayer,
            'currentPlayerScore' => $this->currentPlayer->getHandValue(),
            'player' => $this->player,
            'dealer' => $this->dealer,
            'round' => $this->round,
            'gameOver' => $this->gameOver,
        ];
    }
}
