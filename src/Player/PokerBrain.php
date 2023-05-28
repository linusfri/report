<?php
namespace App\Player;

use App\Helpers\Helper;
use App\PokerGame\PokerGame;

class PokerBrain {
    private const POSSIBILITES = [
        'call',
        'fold',
        'raise',
        'change'
    ];

    /**
     * @var array<string>
     */
    protected array $ideas;

    public function __construct()
    {
        $this->generateNewRoundIdeas();
    }

    public function generateNewRoundIdeas(): void {
        $this->ideas = [];

        foreach (self::POSSIBILITES as $possibility) {
            if (Helper::randomChance()) {
                $this->ideas[] = $possibility;
            }
        }
    }

    public function getRandomIdea(PokerGame $pokerGame): string {
        if (empty($this->ideas)) {
            return 'fold';
        }

        /** 
         * The point of this is to instruct the dealer brain which legal decisions there are
         * based on the current state of the game. For example, it's not possible to check
         * if a bet has been made or if the first round is over. Also, it's not possible
         * to change cards if it's not the second round (card change round).
         */
        $this->ideas = array_values(array_filter($this->ideas, function ($idea) use ($pokerGame) {
            switch ($idea) {
                case 'fold':
                    return true;
                case 'call':
                    return true;
                case 'raise':
                    return true;
                case 'change':
                    return $pokerGame->getCurrentRound() === 2;
                case 'check':
                    return !$pokerGame->getBetHasBeenMade();
            }
        }));

        if (empty($this->ideas)) {
            return Helper::randomChance() ? $this->ideas[] = 'fold' : $this->ideas[] = 'call';
        }

        if ($pokerGame->getCurrentRound() === 2 && !$pokerGame->dealer->getHasChangedCards()) {
            return 'change';
        }

        return $this->ideas[array_rand($this->ideas)];
        // return 'change';
    }
}