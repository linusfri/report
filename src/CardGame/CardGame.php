<?php

namespace App\CardGame;

use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Player\Dealer;
use App\Player\Player;
use App\Player\PlayerInterface;

class CardGame implements \JsonSerializable
{
    private PlayerInterface $currentPlayer;
    public Player $player;
    public Dealer $dealer;
    private int $playerRound;
    private bool $gameOver;
    private DeckOfCards $deck;

    public function __construct(Dealer $dealer, Player $player, DeckOfCards $deck)
    {
        $this->start($dealer, $player, $deck);
    }

    public function start(Dealer $dealer, Player $player, DeckOfCards $deck): bool
    {
        $this->player = $player;
        $this->dealer = $dealer;
        $this->playerRound = 0;
        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleCards();

        $this->currentPlayer = $this->player;

        return true;
    }

    public function currentPlayerDrawCard(): void
    {
        ++$this->playerRound;

        $this->currentPlayer->drawCard($this->deck);

        $this->updateGameState();
    }

    public function dealerDrawCards(): void
    {
        $this->currentPlayer = $this->dealer;

        for ($i = 0; $i < 4; ++$i) {
            if ($this->randomChance()) {
                ++$this->playerRound;
                $this->currentPlayer->drawCard($this->deck);
            }

            if ($this->currentPlayer->getHandValue() >= 21) {
                break;
            }
        }

        $this->currentPlayer->setIsFinished();
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

    private function updateCurrentPlayer(): void
    {
        $this->currentPlayer->countHandValue();

        if ($this->currentPlayer->getHandValue() >= 21) {
            $this->currentPlayer->setIsFinished();
        }
    }

    private function updateDealer(): void
    {
        $this->currentPlayer = $this->dealer;
        $this->currentPlayer->countHandValue();
    }

    public function stopCurrentPlayer(): void
    {
        $this->currentPlayer->setIsFinished();

        $this->updateGameState();
    }

    // private function isCurrentPlayerFinished(): bool
    // {
    //     return $this->currentPlayer->getIsFinished();
    // }

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
        $this->playerRound = 0;

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
        $this->currentPlayer instanceof Dealer ? $this->updateDealer() : $this->updateCurrentPlayer();

        if ($this->currentPlayer->getHandValue() > 21) {
            $this->setGameOver();

            return;
        }

        if ($this->checkAllPlayersFinished()) {
            $this->setGameOver();

            return;
        }

        if (
            21 === $this->currentPlayer->getHandValue() && $this->currentPlayer instanceof Player
        ) {
            $this->dealerDrawCards();

            return;
        }
    }

    public function getCurrentPlayerRound(): int
    {
        return $this->playerRound;
    }

    public function getWinner(): PlayerInterface
    {
        $dealerValue = $this->dealer->getHandValue();
        $playerValue = $this->player->getHandValue();

        if ($dealerValue > 21 && $playerValue > 21) {
            throw new \Exception('Both players can not have more than 21 points.');
        }

        if (21 === $dealerValue || $playerValue > 21 || ($dealerValue <= 21 && $dealerValue > $playerValue)) {
            return $this->dealer;
        }

        return $this->player;
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
            'playerRound' => $this->playerRound,
            'gameOver' => $this->gameOver,
        ];
    }
}
