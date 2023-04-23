<?php

namespace App\Player;

use App\Card\DeckOfCards;
use App\Card\CardGraphic;

interface PlayerInterface
{
    public function __construct(string $name);

    public function drawCard(DeckOfCards $deck): void;

    /**
     * @return Array<CardGraphic>
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
}
