<?php

use App\Card\DeckOfCards;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    private ?DeckOfCards $deck;

    protected function setUp(): void
    {
        $this->deck = new DeckOfCards();
    }

    protected function tearDown(): void
    {
        $this->deck = null;
    }

    public function testGetCardsReturnsArray(): void
    {
        $this->assertIsArray($this->deck->getCards());
    }

    public function testGetCorrectNumberOfCards(): void
    {
        $this->assertEquals(52, $this->deck->getNumberCards());
    }

    public function testSortCardsInOrder(): void
    {
        $this->deck->shuffleCards();
        $this->deck->sortCards();

        $cards = $this->deck->getCards();
        $previousCard = array_shift($cards);

        foreach ($cards as $card) {
            if ($previousCard->getSuit() === $card->getSuit()) {
                $this->assertGreaterThan($previousCard->getValue(), $card->getValue());
            }
            $previousCard = $card;
        }
    }

    public function testShuffleCardsChangesCardOrder(): void
    {
        $originalCards = $this->deck->getCards();
        $this->deck->shuffleCards();

        $shuffledCards = $this->deck->getCards();

        $this->assertNotEquals($originalCards, $shuffledCards);
    }
}

