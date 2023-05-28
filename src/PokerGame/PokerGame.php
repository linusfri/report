<?php

namespace App\PokerGame;

use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\CardGame\CardGame;
use App\PokerGame\PokerHandEvaluator;
use App\Player\Dealer;
use App\Player\Player;
use App\Player\PlayerInterface;
use Exception;

class PokerGame extends CardGame implements \JsonSerializable
{
    protected int $currentRound;
    protected bool $gameOver;
    protected int $currentBet;
    protected int $totalPot;
    protected bool $changeCardRound;
    protected bool $betHasBeenMade;
    protected bool $isShowDown;

    public function __construct(Dealer $dealer, Player $player, DeckOfCards $deck)
    {
        $this->start($dealer, $player, $deck);
    }

    public function start(Dealer $dealer, Player $player, DeckOfCards $deck): bool
    {
        $this->player = $player;
        $this->dealer = $dealer;
        $this->currentRound = 1;
        $this->changeCardRound = false;
        $this->betHasBeenMade = false;
        $this->isShowDown = false;
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

    public function currentPlayerRaise(int $amount): bool
    {
        
        try {
            if ($amount > $this->currentBet && $this->currentPlayer->getMoney() >= $amount) {
                $this->betHasBeenMade = true;
                $bet = $this->currentPlayer->bet($amount);
                $this->totalPot += $bet;

                $this->setcurrentBet($bet);
                $this->currentPlayer->setHasPlayedRound();
                $this->nextPlayer();
            } else {
                throw new Exception('Bet must be higher than current bet. Or player has not enough money.');
            }
        } catch (Exception $e) {
            return false;
        }

        /** 
         * Dont update game state when player raises.
         * This is done when player calls or folds.
         * Reason is that all players should be able to raise indefinitely.
         * 
         * @see $this->updateGameState()
        */

        return true;
    }

    public function currentPlayerCall(): bool
    {
        try {
            if ($this->currentPlayer->getMoney() >= $this->currentBet) {
                $bet = $this->currentPlayer->bet($this->currentBet);
                $this->betHasBeenMade = true;
        
                $this->totalPot += $bet;
                $this->setcurrentBet($bet);
                $this->currentPlayer->setHasPlayedRound();
                $this->nextPlayer();
            } else {
                throw new Exception('Call must be equal to current bet. Or player has not enough money.');
            }
        } catch (Exception $e) {
            return false;
        }

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

    public function pokerDealerDrawCards(int $number): void
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

    public function getCurrentPlayerCards(): array
    {
        return $this->currentPlayer->getCards();
    }

    public function checkAllPlayersPlayedRound(): bool
    {
        return $this->dealer->getHasPlayedRound() && $this->player->getHasPlayedRound();
    }

    public function nextPlayer(): void
    {
        if ($this->checkAllPlayersFinished()) {
            throw new Exception('All players are finished');
        }

        $this->currentPlayer = $this->getCurrentOpponent();

        return;
    }

    public function getCurrentOpponent(): PlayerInterface
    {
        return $this->currentPlayer->getId() !== $this->dealer->getId() ? $this->dealer : $this->player;
    }


    protected function updateGameState(): void
    {
        /** This only works for two players, needs to be changed for arbitrary number of players */
        if ($this->player->getIsFinished() || $this->dealer->getIsFinished()) {
            $this->setGameOver();
            return;
        }

        if ($this->currentRound >= 3 && $this->checkAllPlayersPlayedRound() || ($this->player->getMoney() <= 0 && $this->dealer->getMoney() <= 0)) {
            $this->isShowDown = true;
            return;
        }

        if ($this->player->getHasPlayedRound() && $this->dealer->getHasPlayedRound()) {
            $this->nextRound();
            $this->player->resetHasPlayedRound();
            $this->dealer->resetHasPlayedRound();
            $this->changeCardRound = $this->currentRound === 2 ? true : false;
            $this->betHasBeenMade = false;
        }
    }

    public function getCurrentRound(): int
    {
        return $this->currentRound;
    }

    public function getIsShowDown(): bool
    {
        return $this->isShowDown;
    }

    public function getBetHasBeenMade(): bool
    {
        return $this->betHasBeenMade;
    }

    public function nextRound(): void
    {
        ++$this->currentRound;

        $this->currentPlayer = $this->player;
    }

    public function getPokerWinner(): ?PlayerInterface
    {
        $nonFoldedPlayers = array_values(array_filter([$this->player, $this->dealer], function ($participant) {
            return !$participant->getHasFolded();
        }));

        if(count($nonFoldedPlayers) === 1) {
            return $nonFoldedPlayers[0];
        }

       return PokerHandEvaluator::determineWinner([$this->player, $this->dealer]);
    }

    /**
     * @param array<int> $cardIndices
     */
    public function currentPlayerChangeCards(array $cardIndices): void
    {
        $this->currentPlayer->changeCards($cardIndices, $this->deck);

        $this->updateGameState();
    }

    public function currentPlayerDoneChangeCards(): void
    {
        $this->currentPlayer->setHasChangedCards();
        $this->currentPlayer->setHasPlayedRound();
        $this->nextPlayer();

        $this->updateGameState();
    }

    public function getIsChangeCardRound(): bool
    {
        return $this->changeCardRound;
    }

    public function getPokerLoser(): ?PlayerInterface
    {
        $loser = null;
        if ($this->getPokerWinner()) {
            $loser = $this->getPokerWinner()->getId() !== $this->dealer->getId() ? $this->dealer : $this->player;
        }

        return $loser;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'currentPlayer' => $this->currentPlayer,
            'currentPlayerScore' => $this->currentPlayer->getHandValue(),
            'player' => $this->player,
            'dealer' => $this->dealer,
            'round' => $this->currentRound,
            'gameOver' => $this->gameOver,
        ];
    }
}
