<?php

namespace App\Card;

class Card
{
    public const HEX_TO_SUIT = [
        'A' => 'spades',
        'B' => 'hearts',
        'C' => 'diamonds',
        'D' => 'clubs'
    ];

    protected int $value;
    protected string $suit;

    public function __construct(int $value, string $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }
}
