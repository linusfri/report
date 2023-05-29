<?php

namespace App\PokerGame;

use App\Card\CardGraphic;
use App\Player\PlayerInterface;

class PokerHandEvaluator
{
    public static function determineWinner(array $players): ?PlayerInterface
    {
        $bestHandValue = 0;
        $winningPlayer = null;

        foreach ($players as $player) {
            $handValue = self::evaluateHandValue($player->getCards());

            if ($handValue > $bestHandValue) {
                $bestHandValue = $handValue;
                $winningPlayer = $player;
            }
        }

        return $winningPlayer;
    }

    public static function evaluateHandValue(array $cards): int
    {
        /* Sort the cards by value in ascending order */
        usort($cards, function (CardGraphic $cardA, CardGraphic $cardB) {
            return $cardA->getValue() <=> $cardB->getValue();
        });

        /* Check for different hand combinations in descending order of rank */
        if (self::isRoyalFlush($cards)) {
            return HandRank::ROYAL_FLUSH;
        }

        if (self::isStraightFlush($cards)) {
            return HandRank::STRAIGHT_FLUSH;
        }

        if (self::isFourOfAKind($cards)) {
            return HandRank::FOUR_OF_A_KIND;
        }

        if (self::isFullHouse($cards)) {
            return HandRank::FULL_HOUSE;
        }

        if (self::isFlush($cards)) {
            return HandRank::FLUSH;
        }

        if (self::isStraight($cards)) {
            return HandRank::STRAIGHT;
        }

        if (self::isThreeOfAKind($cards)) {
            return HandRank::THREE_OF_A_KIND;
        }

        if (self::isTwoPair($cards)) {
            return HandRank::TWO_PAIR;
        }

        if (self::isOnePair($cards)) {
            return HandRank::ONE_PAIR;
        }

        return HandRank::HIGH_CARD; // If no other hand combination is found, default to High Card
    }

    /** Check if the cards form a Royal Flush (10, J, Q, K, A of the same suit) */
    public static function isRoyalFlush(array $cards): bool
    {
        $royalFlushValues = [10, 11, 12, 13, 1];
        $suit = $cards[0]->getSuit();

        foreach ($cards as $card) {
            if ($card->getSuit() !== $suit || !in_array($card->getValue(), $royalFlushValues)) {
                return false;
            }
        }

        return true;
    }

    /** Check if the cards form a Straight Flush (consecutive cards of the same suit) */
    public static function isStraightFlush(array $cards): bool
    {
        $suit = $cards[0]->getSuit();

        for ($i = 1; $i < count($cards); ++$i) {
            if ($cards[$i]->getSuit() !== $suit || $cards[$i]->getValue() !== $cards[$i - 1]->getValue() + 1) {
                return false;
            }
        }

        return true;
    }

    /** Check if the cards contain four of the same value */
    public static function isFourOfAKind(array $cards): bool
    {
        $valueCounts = self::getValueCounts($cards);

        foreach ($valueCounts as $count) {
            if (4 === $count) {
                return true;
            }
        }

        return false;
    }

    /** Check if the cards contain a three of a kind and a pair */
    public static function isFullHouse(array $cards): bool
    {
        $valueCounts = self::getValueCounts($cards);

        $hasThreeOfAKind = false;
        $hasPair = false;

        foreach ($valueCounts as $count) {
            if (3 === $count) {
                $hasThreeOfAKind = true;
            } elseif (2 === $count) {
                $hasPair = true;
            }
        }

        return $hasThreeOfAKind && $hasPair;
    }

    /** Check if the cards are all of the same suit */
    public static function isFlush(array $cards): bool
    {
        $suit = $cards[0]->getSuit();

        foreach ($cards as $card) {
            if ($card->getSuit() !== $suit) {
                return false;
            }
        }

        return true;
    }

    /** Check if the cards form a straight (consecutive values) */
    public static function isStraight(array $cards): bool
    {
        for ($i = 1; $i < count($cards); ++$i) {
            if ($cards[$i]->getValue() !== $cards[$i - 1]->getValue() + 1) {
                return false;
            }
        }

        return true;
    }

    /** Check if the cards contain three of the same value */
    public static function isThreeOfAKind(array $cards): bool
    {
        $valueCounts = self::getValueCounts($cards);

        foreach ($valueCounts as $count) {
            if (3 === $count) {
                return true;
            }
        }

        return false;
    }

    /** Check if the cards contain two pairs */
    public static function isTwoPair(array $cards): bool
    {
        $valueCounts = self::getValueCounts($cards);

        $pairCount = 0;

        foreach ($valueCounts as $count) {
            if (2 === $count) {
                ++$pairCount;
            }
        }

        return 2 === $pairCount;
    }

    /** Check if the cards contain a pair */
    public static function isOnePair(array $cards): bool
    {
        $valueCounts = self::getValueCounts($cards);

        foreach ($valueCounts as $count) {
            if (2 === $count) {
                return true;
            }
        }

        return false;
    }

    /** Used when to make a decision on how many cards of each value there is */
    public static function getValueCounts(array $cards): array
    {
        $valueCounts = [];

        foreach ($cards as $card) {
            $value = $card->getValue();

            if (!isset($valueCounts[$value])) {
                $valueCounts[$value] = 0;
            }

            ++$valueCounts[$value];
        }

        return $valueCounts;
    }
}
