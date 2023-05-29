<?php
use PHPUnit\Framework\TestCase;
use App\Player\PokerDealer;
use App\Player\PokerBrain;
use App\Card\CardHand;

class PokerDealerTest extends TestCase
{
    public function testConstructor(): void
    {
        $name = 'John';
        $brain = $this->createMock(PokerBrain::class);
        $cardHand = $this->createMock(CardHand::class);
        $money = 100;

        $dealer = new PokerDealer($name, $brain, $cardHand, $money);

        $this->assertInstanceOf(PokerDealer::class, $dealer);
        $this->assertEquals($name, $dealer->getName());
        $this->assertEquals($money, $dealer->getMoney());
        $this->assertFalse($dealer->getHasFolded());
        $this->assertFalse($dealer->getHasPlayedRound());
        $this->assertFalse($dealer->getHasChangedCards());
        $this->assertEquals(0, $dealer->getHandValue());
        $this->assertFalse($dealer->getIsFinished());
        $this->assertEquals('', $dealer->getPreviousAction());
        $this->assertFalse($dealer->getIsChecked());
        $this->assertIsInt($dealer->getId());
    }

    public function testConstructorWithDefaultArguments(): void
    {
        $name = 'John';

        $dealer = new PokerDealer($name);

        $this->assertInstanceOf(PokerDealer::class, $dealer);
        $this->assertEquals($name, $dealer->getName());
        $this->assertInstanceOf(PokerBrain::class, $dealer->brain);
        $this->assertEquals(0, $dealer->getMoney());
        $this->assertFalse($dealer->getHasFolded());
        $this->assertFalse($dealer->getHasPlayedRound());
        $this->assertFalse($dealer->getHasChangedCards());
        $this->assertEquals(0, $dealer->getHandValue());
        $this->assertFalse($dealer->getIsFinished());
        $this->assertEquals('', $dealer->getPreviousAction());
        $this->assertFalse($dealer->getIsChecked());
        $this->assertIsInt($dealer->getId());
    }
}
