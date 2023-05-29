<?php
namespace App\PokerGame;
/** Translates Card hands to actual "points" */
class HandRank
{
    public const HIGH_CARD = 1;
    public const ROYAL_FLUSH = 10;
    public const STRAIGHT_FLUSH = 9;
    public const FOUR_OF_A_KIND = 8;
    public const FULL_HOUSE = 7;
    public const FLUSH = 6;
    public const STRAIGHT = 5;
    public const THREE_OF_A_KIND = 4;
    public const TWO_PAIR = 3;
    public const ONE_PAIR = 2;
}