<?php
use PHPUnit\Framework\TestCase;
use App\Card\CardHand;
use App\Card\DeckOfCards;
use App\Player\Player;

class PlayerTest extends TestCase
{
    private ?Player $player;
    private ?DeckOfCards $deck;

    protected function setUp(): void
    {
        $cardHand = new CardHand();
        $this->deck = new DeckOfCards();
        $this->player = new Player('Linus', $cardHand);
    }

    protected function tearDown(): void
    {
        $this->player = null;
    }

    public function testDrawCard(): void {
        $this->player->drawCard($this->deck);
        $this->assertNotEmpty($this->player->getCards());
        $this->assertNotEquals(0, $this->player->getHandValue());
    }

    public function testSetHandValueNegative(): void {
        $this->expectException(Exception::class);
        $this->player->setHandValue(-1);
    }

    public function testGetName(): void
    {
        $this->assertEquals('Linus', $this->player->getName());
    }

    public function testGetId(): void
    {
        $this->assertIsInt($this->player->getId());
    }

    public function testGetCardsEmptyArray(): void
    {
        $this->assertIsArray($this->player->getCards());
        $this->assertEmpty($this->player->getCards());
    }

    public function testGetCardsNotEmptyAfterDraw(): void {
        $this->player->drawCard($this->deck);
        $this->assertNotEmpty($this->player->getCards());

        foreach($this->player->getCards() as $card) {
            $this->assertInstanceOf('App\Card\CardGraphic', $card);
        }
    }

    public function testSetIsFinished(): void {
        $this->player->setIsFinished();
        $this->assertTrue($this->player->getIsFinished());
    }

    public function testGetHandValue(): void {
        $this->assertIsInt($this->player->getHandValue());
        $this->assertEquals(0, $this->player->getHandValue());
    }

    public function testSetHandValue(): void {
        $this->player->setHandValue(10);
        $this->assertEquals(10, $this->player->getHandValue());
    }

    public function testReset(): void {
        $this->player->setHandValue(10);
        $this->player->setIsFinished();

        $this->player->reset();
        $this->assertEquals(0, $this->player->getHandValue());
        $this->assertFalse($this->player->getIsFinished());
    }

    public function testSerialize(): void {
        $serializedPlayer = $this->player->jsonSerialize();
        $this->assertIsArray($serializedPlayer);
        $this->assertArrayHasKey('name', $serializedPlayer);
        $this->assertArrayHasKey('id', $serializedPlayer);
        $this->assertArrayHasKey('handValue', $serializedPlayer);
        $this->assertArrayHasKey('isFinished', $serializedPlayer);
        $this->assertArrayHasKey('cards', $serializedPlayer);
    }
}
