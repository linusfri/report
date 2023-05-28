<?php

namespace App\Player;

use App\Card\CardGraphic;
use App\Card\CardHand;
use App\Card\DeckOfCards;
use Exception;
class Player implements PlayerInterface, \JsonSerializable
{
    /**
     * @var CardHand
     *               The players card hand
     * */
    protected CardHand $cardHand;

    /**
     * @var int
     *          The value of the cards in the players hand
     * */
    protected int $handValue;

    /**
     * @var bool
     *           True if player is finished, false if player is not finished
     * */
    protected bool $isFinished;

    /**
     * @var string
     *             The name of the player
     * */
    protected string $name;

    /**
     * @var int
     *          The id of the player
     * */
    protected int $id;

    /**
     * @var int $money
     *          The amount of money the player has
     */
    protected ?int $money;

    /**
     * @var bool $checked
     *          True if player has checked, false if player has not checked
     */
    protected bool $checked;

    /**
     * @var bool $hasPlayedRound
     *          Indicates if the player has played the current round
     */
    protected bool $hasPlayedRound;

    /**
     * @var bool $hasChangedCards
     *          Indicates if the player has changed cards
     */
    protected bool $hasChangedCards;
    
    /**
     * @var bool $hasFolded
     *          Indicates if the player has folded
     */
    protected bool $hasFolded;

    public function __construct(string $name, CardHand $cardHand = new CardHand(), ?int $money = null)
    {
        $this->name = $name;
        $this->money = $money;
        $this->cardHand = $cardHand;
        $this->hasFolded = false;
        $this->hasPlayedRound = false;
        $this->hasChangedCards = false;
        $this->handValue = 0;
        $this->isFinished = false;
        $this->checked = false;
        $this->id = rand(0, 1000000);
    }

    /** Makes player draw a single card from card deck */
    public function drawCard(DeckOfCards $deck): void
    {
        $this->cardHand->drawCards($deck, number: 1);
        $this->countHandValue();
    }

    /**
     * Gets the cards in player's hand.
     *
     * @return array<CardGraphic>
     */
    public function getCards(): array
    {
        return $this->cardHand->cards;
    }

    /** Sets the players status to finished */
    public function setIsFinished(): void
    {
        $this->isFinished = true;
    }

    /** Returns true if player is finished, false if player is not finished */
    public function getIsFinished(): bool
    {
        return $this->isFinished;
    }

    /** Counts the value of the cards in player's hand */
    public function countHandValue(): void
    {
        $this->handValue = array_sum(array_map(function ($card) {
            return $card->getValue();
        }, $this->cardHand->cards));
    }

    /** Returns the value of the cards in player's hand */
    public function getHandValue(): int
    {
        return $this->handValue;
    }

    /** Sets the value of the player's hand, mainly used for testing */
    public function setHandValue(int $value): void
    {
        if ($value < 0) {
            throw new Exception('Hand value cannot be negative');
        }
        $this->handValue = $value;
    }

    /** Returns the name of the player */
    public function getName(): string
    {
        return $this->name;
    }

    /** Returns the id of the player */
    public function getId(): int
    {
        return $this->id;
    }

    /** Resets the player */
    public function reset(): void
    {
        $this->cardHand = new CardHand();
        $this->setHandValue(0);
        $this->isFinished = false;
    }

    /** The player bets money */
    public function bet(int $amount): int
    {   
        if (is_null($this->money)) {
            throw new Exception('The player does not have money. Just plays for fun.');
        }

        /** If bet bigger than current money, go all in */
        if ($this->money < $amount) {
            throw new Exception('The player does not have enough money to bet that amount.');
        }

        $this->money -= $amount;

        return $amount;
    }

    /** Returns current player money */
    public function getMoney(): int
    {
        return $this->money ?? 0;
    }

    /** The player checks  */
    public function check(): void
    {
        $this->checked = true;
    }

    /** Returns if current player has checked */
    public function getIsChecked(): bool
    {
        return $this->checked;
    }

    /** Player folds */
    public function fold(): void
    {
        $this->isFinished = true;
        $this->hasFolded = true;
    }

    /** Sets that player has played current round */
    public function setHasPlayedRound(): void
    {
        $this->hasPlayedRound = true;
    }

    /** Resets player has played round */
    public function resetHasPlayedRound(): void
    {
        $this->hasPlayedRound = false;
    }

    /** Gets that player has played current round */
    public function getHasPlayedRound(): bool
    {
        return $this->hasPlayedRound;
    }

    /**
     * changeCards
     *
     * @param array<int> $cardIndices
     * @return void
     */
    public function changeCards(array $cardIndices, DeckOfCards $cardDeck): void {
        if ($cardDeck->getNumberCards() < count($cardIndices)) {
            throw new Exception('Not enough cards in deck to change cards');
        }

        foreach ($cardIndices as $index) {
            $this->cardHand->changeCardAtIndex($index, $cardDeck);
        }
        $this->hasChangedCards = true;
    }

    /** Get has changed cards */
    public function getHasChangedCards(): bool
    {
        return $this->hasChangedCards;
    }

    /** Set has changed cards */
    public function setHasChangedCards(): void
    {
        $this->hasChangedCards = true;
    }

    /** Set has folded */
    public function setHasFolded(): void
    {
        $this->hasFolded = true;
    }

    /** Get has folded */
    public function getHasFolded(): bool
    {
        return $this->hasFolded;
    }

    /**
     * Returns the player as an array of keys and values.
     * This is for specifying what data to include when serializing the player to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'handValue' => $this->getHandValue(),
            'isFinished' => $this->getIsFinished(),
            'cards' => $this->getCards(),
        ];
    }
}
