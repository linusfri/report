<?php
use PHPUnit\Framework\TestCase;
use App\Card\CardGraphic;
use App\PokerGame\PokerHandEvaluator;

class PokerHandEvaluatorTest extends TestCase
{
    public function testIsFourOfAKind()
    {
        $cards = [
            new CardGraphic(2, 'Spades', '2S'),
            new CardGraphic(2, 'Hearts', '2H'),
            new CardGraphic(2, 'Diamonds', '2D'),
            new CardGraphic(2, 'Clubs', '2C'),
            new CardGraphic(5, 'Spades', '5S'),
        ];

        $result = PokerHandEvaluator::isFourOfAKind($cards);

        $this->assertTrue($result);
    }

    public function testIsFullHouse()
    {
        $cards = [
            new CardGraphic(3, 'Spades', '3S'),
            new CardGraphic(3, 'Hearts', '3H'),
            new CardGraphic(3, 'Diamonds', '3D'),
            new CardGraphic(6, 'Clubs', '6C'),
            new CardGraphic(6, 'Spades', '6S'),
        ];

        $result = PokerHandEvaluator::isFullHouse($cards);

        $this->assertTrue($result);
    }

    public function testIsFlush()
    {
        $cards = [
            new CardGraphic(4, 'Hearts', '4H'),
            new CardGraphic(7, 'Hearts', '7H'),
            new CardGraphic(9, 'Hearts', '9H'),
            new CardGraphic(11, 'Hearts', '11H'),
            new CardGraphic(13, 'Hearts', 'KH'),
        ];

        $result = PokerHandEvaluator::isFlush($cards);

        $this->assertTrue($result);
    }

    public function testIsStraight()
    {
        $cards = [
            new CardGraphic(6, 'Spades', '6S'),
            new CardGraphic(7, 'Hearts', '7H'),
            new CardGraphic(8, 'Diamonds', '8D'),
            new CardGraphic(9, 'Clubs', '9C'),
            new CardGraphic(10, 'Spades', '10S'),
        ];

        $result = PokerHandEvaluator::isStraight($cards);

        $this->assertTrue($result);
    }

    public function testIsThreeOfAKind()
    {
        $cards = [
            new CardGraphic(5, 'Spades', '5S'),
            new CardGraphic(5, 'Hearts', '5H'),
            new CardGraphic(5, 'Diamonds', '5D'),
            new CardGraphic(8, 'Clubs', '8C'),
            new CardGraphic(9, 'Spades', '9S'),
        ];

        $result = PokerHandEvaluator::isThreeOfAKind($cards);

        $this->assertTrue($result);
    }

    public function testIsTwoPair()
    {
        $cards = [
            new CardGraphic(7, 'Spades', '7S'),
            new CardGraphic(7, 'Hearts', '7H'),
            new CardGraphic(9, 'Diamonds', '9D'),
            new CardGraphic(9, 'Clubs', '9C'),
            new CardGraphic(11, 'Spades', 'KS'),
        ];

        $result = PokerHandEvaluator::isTwoPair($cards);

        $this->assertTrue($result);
    }

    public function testIsOnePair()
    {
        $cards = [
            new CardGraphic(10, 'Spades', '10S'),
            new CardGraphic(10, 'Hearts', '10H'),
            new CardGraphic(4, 'Diamonds', '4D'),
            new CardGraphic(7, 'Clubs', '7C'),
            new CardGraphic(13, 'Spades', 'KS'),
        ];

        $result = PokerHandEvaluator::isOnePair($cards);

        $this->assertTrue($result);
    }

    // public function testGetHandRank()
    // {
    //     $cards = [
    //         new CardGraphic(4, 'Spades', '4S'),
    //         new CardGraphic(4, 'Hearts', '4H'),
    //         new CardGraphic(4, 'Diamonds', '4D'),
    //         new CardGraphic(4, 'Clubs', '4C'),
    //         new CardGraphic(5, 'Spades', '5S'),
    //     ];

    //     $expectedRank = HandRank::FOUR_OF_A_KIND;
    //     $result = PokerHandEvaluator::getHandRank($cards);

    //     $this->assertEquals($expectedRank, $result);
    // }
}
