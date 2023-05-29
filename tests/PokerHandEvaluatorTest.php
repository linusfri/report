<?php
use PHPUnit\Framework\TestCase;
use App\Card\CardGraphic;
use App\PokerGame\PokerHandEvaluator;

class PokerHandEvaluatorTest extends TestCase
{
    public function testIsFourOfAKind()
    {
        $cards = [
            new CardGraphic(2, 'Spades', ''),
            new CardGraphic(2, 'Hearts', ''),
            new CardGraphic(2, 'Diamonds', ''),
            new CardGraphic(2, 'Clubs', ''),
            new CardGraphic(5, 'Spades', ''),
        ];

        $result = PokerHandEvaluator::isFourOfAKind($cards);

        $this->assertTrue($result);
    }

    public function testIsFullHouse()
    {
        $cards = [
            new CardGraphic(3, 'Spades', ''),
            new CardGraphic(3, 'Hearts', ''),
            new CardGraphic(3, 'Diamonds', ''),
            new CardGraphic(6, 'Clubs', ''),
            new CardGraphic(6, 'Spades', ''),
        ];

        $result = PokerHandEvaluator::isFullHouse($cards);

        $this->assertTrue($result);
    }

    public function testIsFlush()
    {
        $cards = [
            new CardGraphic(4, 'Hearts', ''),
            new CardGraphic(7, 'Hearts', ''),
            new CardGraphic(9, 'Hearts', ''),
            new CardGraphic(11, 'Hearts', ''),
            new CardGraphic(13, 'Hearts', ''),
        ];

        $result = PokerHandEvaluator::isFlush($cards);

        $this->assertTrue($result);
    }

    public function testIsStraight()
    {
        $cards = [
            new CardGraphic(6, 'Spades', ''),
            new CardGraphic(7, 'Hearts', ''),
            new CardGraphic(8, 'Diamonds', ''),
            new CardGraphic(9, 'Clubs', ''),
            new CardGraphic(10, 'Spades', ''),
        ];

        $result = PokerHandEvaluator::isStraight($cards);

        $this->assertTrue($result);
    }

    public function testIsThreeOfAKind()
    {
        $cards = [
            new CardGraphic(5, 'Spades', ''),
            new CardGraphic(5, 'Hearts', ''),
            new CardGraphic(5, 'Diamonds', ''),
            new CardGraphic(8, 'Clubs', ''),
            new CardGraphic(9, 'Spades', ''),
        ];

        $result = PokerHandEvaluator::isThreeOfAKind($cards);

        $this->assertTrue($result);
    }

    public function testIsTwoPair()
    {
        $cards = [
            new CardGraphic(7, 'Spades', ''),
            new CardGraphic(7, 'Hearts', ''),
            new CardGraphic(9, 'Diamonds', ''),
            new CardGraphic(9, 'Clubs', ''),
            new CardGraphic(11, 'Spades', ''),
        ];

        $result = PokerHandEvaluator::isTwoPair($cards);

        $this->assertTrue($result);
    }

    public function testIsOnePair()
    {
        $cards = [
            new CardGraphic(10, 'Spades', ''),
            new CardGraphic(10, 'Hearts', ''),
            new CardGraphic(4, 'Diamonds', ''),
            new CardGraphic(7, 'Clubs', ''),
            new CardGraphic(13, 'Spades', ''),
        ];

        $result = PokerHandEvaluator::isOnePair($cards);

        $this->assertTrue($result);
    }
}
