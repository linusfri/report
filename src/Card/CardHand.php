<?php

namespace App\Card;

class CardHand
{
    /**
     * Cards.
     *
     * @var array<CardGraphic>
     */
    public array $cards;

    public function __construct()
    {
        $this->cards = [];
    }

    public function drawCards(DeckOfCards $deck, int $number = 1): void
    {
        if (!count($deck->cards)) {
            return;
        }

        if ($number <= 0 || $number > count($deck->cards)) {
            return;
        }

        for ($i = 0; $i < $number; ++$i) {
            array_push($this->cards, array_pop($deck->cards));
        }
    }

    public function changeCardAtIndex(int $index, DeckOfCards $deck) {
        $this->cards[$index] = array_pop($deck->cards);
    }
}
