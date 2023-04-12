<?php

namespace App\Card;

class DeckOfCards
{
    /**
     * @cards
     *  
     * @var array<CardGraphic>
     */
    public array $cards;

    public function __construct()
    {
        $this->cards = [];
        $this->generateCardDeck();
        $this->sortCards();
    }

    /**
     * getCards
     *
     * @return array<CardGraphic>
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function sortCards(): void
    {
        usort($this->cards, function (CardGraphic $cardA, CardGraphic $cardB) {
            return [$cardA->getSuit(), $cardA->getValue()] <=> [$cardB->getSuit(), $cardB->getValue()];
        });
    }

    public function shuffleCards(): void
    {
        shuffle($this->cards);
    }

    private function generateCardDeck(): void
    {
        for ($i = 1; $i <= 13; ++$i) {
            for ($j = 10; $j <= 13; ++$j) {
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

    public function getNumberCards(): int
    {
        return count($this->cards);
    }
}
