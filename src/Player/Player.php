<?php

namespace App\Player;

use App\Card\CardGraphic;
use App\Card\CardHand;
use App\Card\DeckOfCards;
use Exception;
class Player implements PlayerInterface, \JsonSerializable
{
    /**
     * @var CardHand
     *               The players card hand
     * */
    protected CardHand $cardHand;

    /**
     * @var int
     *          The value of the cards in the players hand
     * */
    protected int $handValue;

    /**
     * @var bool
     *           True if player is finished, false if player is not finished
     * */
    protected bool $isFinished;

    /**
     * @var string
     *             The name of the player
     * */
    protected string $name;

    /**
     * @var int
     *          The id of the player
     * */
    protected int $id;

    /**
     * @var int $money
     *          The amount of money the player has
     */
    protected ?int $money;

    public function __construct(string $name, CardHand $cardHand = new CardHand(), ?int $money = null)
    {
        $this->name = $name;
        $this->money = $money;
        $this->cardHand = $cardHand;
        $this->handValue = 0;
        $this->isFinished = false;
        $this->id = rand(0, 1000000);
    }

    /** Makes player draw a single card from card deck */
    public function drawCard(DeckOfCards $deck): void
    {
        $this->cardHand->drawCards($deck, number: 1);
        $this->countHandValue();
    }

    /**
     * Gets the cards in player's hand.
     *
     * @return array<CardGraphic>
     */
    public function getCards(): array
    {
        return $this->cardHand->cards;
    }

    /** Sets the players status to finished */
    public function setIsFinished(): void
    {
        $this->isFinished = true;
    }

    /** Returns true if player is finished, false if player is not finished */
    public function getIsFinished(): bool
    {
        return $this->isFinished;
    }

    /** Counts the value of the cards in player's hand */
    public function countHandValue(): void
    {
        $this->handValue = array_sum(array_map(function ($card) {
            return $card->getValue();
        }, $this->cardHand->cards));
    }

    /** Returns the value of the cards in player's hand */
    public function getHandValue(): int
    {
        return $this->handValue;
    }

    /** Sets the value of the player's hand, mainly used for testing */
    public function setHandValue(int $value): void
    {
        if ($value < 0) {
            throw new Exception('Hand value cannot be negative');
        }
        $this->handValue = $value;
    }

    /** Returns the name of the player */
    public function getName(): string
    {
        return $this->name;
    }

    /** Returns the id of the player */
    public function getId(): int
    {
        return $this->id;
    }

    /** Resets the player */
    public function reset(): void
    {
        $this->cardHand = new CardHand();
        $this->setHandValue(0);
        $this->isFinished = false;
    }

    /** The player bets money */
    public function bet(int $amount): int
    {   
        if (is_null($this->money)) {
            throw new Exception('The player does not have money. Just plays for fun.');
        }

        /** If bet bigger than current money, go all in */
        if ($this->money < $amount) {
            $bet = $this->money;
            $this->money -= $bet;

            return $bet;
        }

        $this->money -= $amount;

        return $amount;
    }

    /** The player checks previous player bet */
    public function check(int $previousPlayerBetAmount): int
    {
        if ($previousPlayerBetAmount > $this->money) {
            $allRemainingMoneyBet = $this->money;
            $this->money = 0;

            return $allRemainingMoneyBet;
        }

        $this->money -= $previousPlayerBetAmount;

        return $previousPlayerBetAmount;
    }

    /** Player folds */
    public function fold(): void
    {
        $this->isFinished = true;
    }

    /**
     * Returns the player as an array of keys and values.
     * This is for specifying what data to include when serializing the player to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'handValue' => $this->getHandValue(),
            'isFinished' => $this->getIsFinished(),
            'cards' => $this->getCards(),
        ];
    }
}
