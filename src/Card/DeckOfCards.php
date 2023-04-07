<?php

namespace App\Card;

use App\Card\CardGraphic;

class DeckOfCards
{
    public array $cards;
    private bool $includeKnight;

    public function __construct($includeKnight = false)
    {
        $this->includeKnight = $includeKnight;
        $this->cards = [];
        $this->generateCardDeck();
        $this->sortCards();
    }

    public function showCards()
    {

    }

    private function sortCards()
    {
        usort($this->cards, function (CardGraphic $a, CardGraphic $b) {
            return [$a->getSuit(), $a->getValue()] <=> [$b->getSuit(), $b->getValue()];
        });
    }

    public function shuffleCards(): void
    {
        shuffle($this->cards);
    }

    public function drawCard($number = 1): Card
    {
        return end($this->cards);
    }

    private function generateCardDeck(): void
    {
        if ($this->includeKnight) {
            for ($i = 1; $i <= 14; $i++) {
                for ($j = 10; $j <= 13; $j++) {
                    $cardHexValue = strtoupper(dechex($i));
                    $suitHexValue = strtoupper(dechex($j));
                    $suit = CardGraphic::HEX_TO_SUIT[$suitHexValue];

                    $utf8Representation = "&#x1F0{$suitHexValue}{$cardHexValue}";

                    array_push($this->cards, new CardGraphic($i, $suit, $utf8Representation));
                }
            }

            return;
        }

        for ($i = 1; $i <= 13; $i++) {
            for ($j = 10; $j <= 13; $j++) {
                $cardHexValue = $i >= 12
                ? strtoupper(dechex($i + 1))
                : strtoupper(dechex($i));

                $suitHexValue = strtoupper(dechex($j));
                $suit = CardGraphic::HEX_TO_SUIT[$suitHexValue];

                $utf8Representation = "&#x1F0{$suitHexValue}{$cardHexValue}";

                array_push($this->cards, new CardGraphic($i, $suit, $utf8Representation));
            }
        }
    }

    public function getNumberCards()
    {
        return count($this->cards);
    }
}
