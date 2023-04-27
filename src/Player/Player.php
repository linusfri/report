<?php

namespace App\Player;

use App\Card\CardHand;
use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use \Exception;

class Player implements PlayerInterface, \JsonSerializable
{
    protected CardHand $cardHand;
    protected int $handValue;
    protected bool $isFinished;
    protected string $name;
    protected int $id;

    public function __construct(string $name, CardHand $cardHand = new CardHand())
    {
        $this->name = $name;
        $this->cardHand = $cardHand;
        $this->handValue = 0;
        $this->isFinished = false;
        $this->id = rand(0, 1000000);
    }

    public function drawCard(DeckOfCards $deck): void
    {
        $this->cardHand->drawCards($deck, number: 1);
        $this->countHandValue();
    }

    /**
     * @return Array<CardGraphic>
     */
    public function getCards(): array
    {
        return $this->cardHand->cards;
    }

    public function setIsFinished(): void
    {
        $this->isFinished = true;
    }

    public function getIsFinished(): bool
    {
        return $this->isFinished;
    }

    public function countHandValue(): void
    {
        $this->handValue = array_sum(array_map(function ($card) {
            return $card->getValue();
        }, $this->cardHand->cards));
    }

    public function getHandValue(): int
    {
        return $this->handValue;
    }

    public function setHandValue(int $value): void
    {
        if ($value < 0) {
            throw new \Exception('Hand value cannot be negative');
        }
        $this->handValue = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function reset(): void
    {
        $this->cardHand = new CardHand();
        $this->setHandValue(0);
        $this->isFinished = false;
    }

    public function jsonSerialize(): mixed
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
