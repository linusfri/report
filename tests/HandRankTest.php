<?php
use PHPUnit\Framework\TestCase;
use App\PokerGame\HandRank;

class HandRankTest extends TestCase
{
    public function testHandRankConstants()
    {
        $this->assertSame(1, HandRank::HIGH_CARD);
        $this->assertSame(10, HandRank::ROYAL_FLUSH);
        $this->assertSame(9, HandRank::STRAIGHT_FLUSH);
        $this->assertSame(8, HandRank::FOUR_OF_A_KIND);
        $this->assertSame(7, HandRank::FULL_HOUSE);
        $this->assertSame(6, HandRank::FLUSH);
        $this->assertSame(5, HandRank::STRAIGHT);
        $this->assertSame(4, HandRank::THREE_OF_A_KIND);
        $this->assertSame(3, HandRank::TWO_PAIR);
        $this->assertSame(2, HandRank::ONE_PAIR);
    }

    
}