<?php
namespace App\Player;

use App\Card\DeckOfCards;
use App\Card\CardHand;

interface PlayerInterface {
    public function __construct(string $name);

    public function drawCard(DeckOfCards $deck): void;

    public function getCards(): array;

    public function countHandValue(): void;

    public function setIsFinished(bool $value): void;

    public function getHandValue(): int;

    public function setHandValue(int $value): void;

    public function getIsFinished(): bool;

    public function getName(): string;

    public function getId(): int;

    public function reset(): void;
}