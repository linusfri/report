<?php

namespace App\Player;

use App\Card\CardGraphic;
use App\Card\DeckOfCards;

interface PlayerInterface
{
    public function __construct(string $name);

    public function drawCard(DeckOfCards $deck): void;

    /**
     * @return array<CardGraphic>
     */
    public function getCards(): array;

    public function countHandValue(): void;

    public function setIsFinished(): void;

    public function getHandValue(): int;

    public function setHandValue(int $value): void;

    public function getIsFinished(): bool;

    public function getName(): string;

    public function getId(): int;

    public function reset(): void;

    public function bet(int $value): int;

    public function check(): void;

    public function getIsChecked(): bool;

    public function fold(): void;

    public function getMoney(): int;

    public function setMoney(int $amount): void;

    public function setHasPlayedRound(): void;

    public function getHasPlayedRound(): bool;

    public function changeCards(array $cardIndices, DeckOfCards $cardDeck): void;

    public function resetHasPlayedRound(): void;

    public function setHasChangedCards(): void;

    public function getPreviousAction(): string;

    public function setPreviousAction(string $action): void;
}
