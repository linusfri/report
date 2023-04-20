<?php
namespace App\Player;

use App\Card\DeckOfCards;
use App\Card\CardHand;

class Player implements PlayerInterface {
    protected CardHand $cardHand;
    protected int $handValue;
    protected bool $isFinished;
    protected string $name;
    protected int $id;

    public function __construct(string $name, CardHand $cardHand = new CardHand()) {
        $this->name = $name;
        $this->cardHand = $cardHand;
        $this->handValue = 0;
        $this->isFinished = false;
        $this->id = rand(0, 1000000);
    }

    public function drawCard(DeckOfCards $deck): void {
        $this->cardHand->drawCards($deck, number: 1);
    }

    public function getCards(): array {
        return $this->cardHand->cards;
    }

    public function setIsFinished(bool $value = true): void {
        $this->isFinished = $value;
    }

    public function getIsFinished(): bool {
        return $this->isFinished;
    }

    public function countHandValue(): void
    {
        $this->handValue = array_sum(array_map(function($card) {
            return $card->getValue();
        }, $this->cardHand->cards));
    }

    public function getHandValue(): int {
        return $this->handValue;
    }

    public function setHandValue(int $value): void {
        $this->handValue = $value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function reset(): void {
        $this->cardHand = new CardHand();
        $this->setHandValue(0);
        $this->setIsFinished(false);
    }
}