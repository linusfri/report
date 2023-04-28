<?php
namespace App\Card;

use PHPUnit\Framework\TestCase;

class CardGraphicTest extends TestCase
{
    private ?CardGraphic $card;

    public function setUp(): void {
        $this->card = new CardGraphic(1, 'spades', '&#x1F0A1');
    }
    public function testGetInstance(): void
    {
        $this->assertInstanceOf('\App\Card\CardGraphic', $this->card);
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
        $this->assertObjectHasProperty('utf8Representation', $this->card);
    }

    public function testIsHexToSuitArray(): void
    {
        $this->assertIsArray($this->card::HEX_TO_SUIT);
        $this->assertIsIterable($this->card::HEX_TO_SUIT);
    }

    public function testUtf8Rep(): void
    {
        $this->assertEquals('&#x1F0A1', $this->card->getUtf8Rep());
    }

    public function testSerialize(): void
    {
        $this->assertIsArray($this->card->jsonSerialize());
        $this->assertIsIterable($this->card->jsonSerialize());
        $this->assertArrayHasKey('suit', $this->card->jsonSerialize());
        $this->assertArrayHasKey('value', $this->card->jsonSerialize());
        $this->assertArrayHasKey('utf8', $this->card->jsonSerialize());
    }

    public function tearDown(): void {
        $this->card = null;
    }
}