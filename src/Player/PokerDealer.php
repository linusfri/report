<?php

namespace App\Player;

use App\Card\CardHand;

class PokerDealer extends Dealer implements DealerInterface
{
    public PokerBrain $brain;

    public function __construct(
        string $name,
        PokerBrain $brain = new PokerBrain(),
        CardHand $cardHand = new CardHand(),
        ?int $money = null
    ) {
        $this->name = $name;
        $this->brain = $brain;
        $this->cardHand = $cardHand;
        $this->money = $money;
        $this->cardHand = $cardHand;
        $this->hasFolded = false;
        $this->hasPlayedRound = false;
        $this->hasChangedCards = false;
        $this->handValue = 0;
        $this->isFinished = false;
        $this->previousAction = '';
        $this->checked = false;
        $this->id = rand(0, 1000000);
    }
}
