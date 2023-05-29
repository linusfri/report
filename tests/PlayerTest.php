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
        $this->player = new Player('Linus', $cardHand, money: 100);
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

    public function testBetWithEnoughMoney(): void
    {
        $amount = 50;
        $result = $this->player->bet($amount);

        $this->assertEquals(50, $result);
        $this->assertEquals(50, $this->player->getMoney());
    }

    public function testBetWithNotEnoughMoney(): void
    {
        $this->expectException(Exception::class);

        $amount = 150;
        $this->player->bet($amount);
    }

    public function testCheck(): void
    {
        $this->player->check();

        $this->assertTrue($this->player->getIsChecked());
    }

    public function testFold(): void
    {
        $this->player->fold();

        $this->assertTrue($this->player->getHasFolded());
        $this->assertTrue($this->player->getIsFinished());
    }

    public function testChangeCards(): void
    {
        /** Assume the deck has enough cards for testing */

        $this->player->changeCards([0, 1, 2], $this->deck);

        $this->assertTrue($this->player->getHasChangedCards());
    }

    public function testSetAndGetPreviousAction(): void
    {
        $this->player->setPreviousAction('bet');

        $this->assertEquals('bet', $this->player->getPreviousAction());
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
