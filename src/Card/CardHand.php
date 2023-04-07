<?php

namespace App\Card;

class CardHand
{
    public string $here;
    public array $cards;

    public function __construct()
    {
        $this->here = 'im here';
        $this->cards = [];
    }

    public function drawCards(DeckOfCards $deck, int $number = 1): void
    {
        if (! count($deck->cards)) {
            return;
        }

        if ($number <= 0 || $number > count($deck->cards)) {
            return;
        }

        for ($i = 0; $i < $number; $i++) {
            array_push($this->cards, array_pop($deck->cards));
        }
    }
}
