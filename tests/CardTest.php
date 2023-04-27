<?php
namespace App\Card;

use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    private ?Card $card;

    public function setUp(): void
    {
        $this->card = new Card(1, 'spades');
    }
    public function testGetInstance(): void
    {
        $this->assertInstanceOf('\App\Card\Card', $this->card);
    }

    public function testGetValue(): void
    {
        $this->assertEquals(1, $this->card->getValue());
    }

    public function testGetSuit(): void
    {
        $this->assertEquals('spades', $this->card->getSuit());
    }

    public function testHasExpectedAttributes(): void
    {
        $this->assertObjectHasProperty('value', $this->card);
        $this->assertObjectHasProperty('suit', $this->card);
    }

    public function testIsHexToSuitArray(): void
    {
        $this->assertIsArray($this->card::HEX_TO_SUIT);
        $this->assertIsIterable($this->card::HEX_TO_SUIT);
    }
}