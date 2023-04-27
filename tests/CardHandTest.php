<?php
use PHPUnit\Framework\TestCase;
use App\Card\CardHand;
use App\Card\DeckOfCards;

class CardHandTest extends TestCase
{
    private ?CardHand $cardHand;
    private ?DeckOfCards $deck;

    protected function setUp(): void
    {
        $this->cardHand = new CardHand();
        $this->deck = new DeckOfCards();
    }

    protected function tearDown(): void
    {
        $this->deck = null;
    }

    public function testDrawCards(): void
    {
        $this->cardHand->drawCards($this->deck, 1);
        $this->assertCount(1, $this->cardHand->cards);
    }

    public function testDrawCardsWithEmptyDeck(): void
    {
        $this->deck->cards = [];
        $this->cardHand->drawCards($this->deck, 1);
        $this->assertCount(0, $this->cardHand->cards);
    }

    public function testDrawCardsWithInvalidNumber(): void
    {
        $this->cardHand->drawCards($this->deck, -1);
        $this->assertCount(0, $this->cardHand->cards);
    }

    public function testDrawMaxNumberCards(): void
    {
        $this->cardHand->drawCards($this->deck, 52);
        $this->assertCount(52, $this->cardHand->cards);
    }

    public function testDrawCardsMoreThanMax(): void
    {
        $this->cardHand->drawCards($this->deck, 53);
        $this->assertCount(0, $this->cardHand->cards);
    }
}
