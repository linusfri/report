<?php

namespace Tests\CardGame;

use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Card\CardHand;
use App\CardGame\CardGame;
use App\Player\Dealer;
use App\Player\Player;
use PHPUnit\Framework\TestCase;

class CardGameTest extends TestCase
{
    private ?CardGame $cardGame;

    public function setUp(): void
    {
        $deckOfCards = new DeckOfCards();
        $player = new Player('Linus', new CardHand());
        $dealer = new Dealer('Mos', new CardHand());
        $this->cardGame = new CardGame($dealer, $player, $deckOfCards);
    }

    public function testSetsPropertiesCorrectly(): void
    {
        $this->assertSame($this->cardGame->player, $this->cardGame->getCurrentPlayer());
        $this->assertSame(0, $this->cardGame->getCurrentPlayerRound());
        $this->assertFalse($this->cardGame->isGameOver());
    }

    public function testCurrentPlayerDrawCardAddsCardToPlayerHand(): void
    {
        $this->cardGame->currentPlayerDrawCard();

        $this->assertCount(1, $this->cardGame->getCurrentPlayerCards());
    }

    public function testDealerDrawCardsCalled(): void
    {
        $mockCardGame = $this->createMock(CardGame::class);
        $mockCardGame->expects($this->once())
                    ->method('dealerDrawCards');

        $mockCardGame->dealerDrawCards();    
    }

    public function testStopCurrentPlayerSetsIsFinishedToTrue(): void
    {
        $this->cardGame->stopCurrentPlayer();

        $this->assertTrue($this->cardGame->player->getIsFinished());
    }

    public function testCheckAllPlayersFinishedReturnsTrueWhenBothPlayersAreFinished(): void
    {
        $this->cardGame->player->setIsFinished(true);
        $this->cardGame->dealer->setIsFinished(true);

        $result = $this->cardGame->checkAllPlayersFinished();

        $this->assertTrue($result);
    }

    public function testNextPlayerMethodSwitchesToCorrectPlayer(): void
    {
        $this->assertInstanceOf(Player::class, $this->cardGame->getCurrentPlayer());

        $this->cardGame->nextPlayer();

        $this->assertInstanceOf(Dealer::class, $this->cardGame->getCurrentPlayer());
    }

    public function testGetWinnerMethodReturnsCorrectWinnerWhenEqual(): void
    {
        $this->cardGame->getCurrentPlayer()->setHandValue(21);
        $this->cardGame->nextPlayer();
        $this->cardGame->getCurrentPlayer()->setHandValue(21);

        $winner = $this->cardGame->getWinner();

        $this->assertSame($this->cardGame->dealer, $winner);
    }
}

