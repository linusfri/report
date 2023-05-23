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
    private PlayerInterface $currentPlayer;
    public Player $player;
    public Dealer $dealer;
    private int $round;
    private bool $gameOver;
    private DeckOfCards $deck;
    private int $previousBet;

    public function __construct(Dealer $dealer, Player $player, DeckOfCards $deck)
    {
        $this->start($dealer, $player, $deck);
    }

    public function start(Dealer $dealer, Player $player, DeckOfCards $deck): bool
    {
        $this->player = $player;
        $this->dealer = $dealer;
        $this->round = 1;
        $this->previousBet = 0;
        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleCards();

        $this->currentPlayer = $this->player;

        $this->dealCards();

        return true;
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
        $this->currentPlayer->setIsFinished();

        $this->updateGameState();
    }

    public function currentPlayerBet(int $amount): void
    {
        $this->currentPlayer->bet($amount);

        $this->updateGameState();
    }

    public function currentPlayerCheck(): void
    {

        $this->updateGameState();
    }

    public function setPreviousBet(int $amount): void
    {
        $this->previousBet = $amount;
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
            return;
        }

        $this->currentPlayer = $this->currentPlayer instanceof Player ? $this->dealer : $this->player;

        $this->updateGameState();

        return;
    }

    public function getCurrentPlayer(): PlayerInterface
    {
        return $this->currentPlayer;
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
        // TODO for poker
    }

    public function getCurrentRound(): int
    {
        return $this->round;
    }

    public function nextRound(): void
    {
        ++$this->round;
    }

    public function getWinner(): PlayerInterface
    {
        // Todo
        return new Player('todo');
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
