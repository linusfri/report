<?php

namespace Tests\CardGame;

use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use App\Card\CardHand;
use App\CardGame\CardGame;
use App\Player\Dealer;
use App\Player\Player;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionClass;

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

    public function testGameOverWhenPlayerHasMoreThan21(): void
    {
        while ($this->cardGame->getCurrentPlayer()->getHandValue() <= 21) {
            $this->cardGame->currentPlayerDrawCard();
        }

        $this->assertTrue($this->cardGame->isGameOver());
    }

    public function testUpdateGameStateSetsGameOverWhenBothPlayersFinished(): void {
        $this->cardGame->player->setIsFinished();
        $this->cardGame->dealer->setIsFinished();

        $method = new ReflectionMethod($this->cardGame::class, 'updateGameState');
        $method->setAccessible(true);
        $method->invoke($this->cardGame);

        $this->assertTrue($this->cardGame->isGameOver());
    }

    public function testCheckAllPlayersFinishedReturnsTrueWhenBothPlayersAreFinished(): void
    {
        $this->cardGame->player->setIsFinished();
        $this->cardGame->dealer->setIsFinished();

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

    public function testSetGameOver(): void {
        $this->cardGame->setGameOver();

        $this->assertTrue($this->cardGame->isGameOver());
    }

    public function testReset(): void {
        $this->cardGame->reset();

        $this->assertSame(0, $this->cardGame->getCurrentPlayerRound());
        $this->assertFalse($this->cardGame->isGameOver());
        $this->assertCount(0, $this->cardGame->getCurrentPlayerCards());
        $this->assertCount(0, $this->cardGame->player->getCards());
        $this->assertCount(0, $this->cardGame->dealer->getCards());
    }

    public function testStopCurrentPlayer(): void {
        $this->cardGame->stopCurrentPlayer();

        $this->assertTrue($this->cardGame->getCurrentPlayer()->getIsFinished());
    }

    public function testJsonSerialize(): void {
        $serializedGame = $this->cardGame->jsonSerialize();
        $this->assertIsArray($serializedGame);

        $this->assertArrayHasKey('player', $serializedGame);
        $this->assertArrayHasKey('dealer', $serializedGame);
        $this->assertArrayHasKey('playerRound', $serializedGame);
        $this->assertArrayHasKey('gameOver', $serializedGame);
        $this->assertArrayHasKey('currentPlayer', $serializedGame);
        $this->assertArrayHasKey('currentPlayerScore', $serializedGame);

    }

    public function testRandomChance(): void
    {
        $method = new ReflectionMethod($this->cardGame::class, 'randomChance');
        $method->setAccessible(true);
        $result = $method->invoke($this->cardGame);

        $this->assertContains($result, [true, false]);
    }

    public function testGetLoserWhenDealerLost(): void {
        $this->cardGame->getCurrentPlayer()->setHandValue(21);
        $this->cardGame->nextPlayer();
        $this->cardGame->getCurrentPlayer()->setHandValue(22);

        $loser = $this->cardGame->getLoser();

        $this->assertSame($this->cardGame->dealer, $loser);
    }

    public function testGetLoserWhenPlayerLost(): void {
        $this->cardGame->getCurrentPlayer()->setHandValue(22);
        $this->cardGame->nextPlayer();
        $this->cardGame->getCurrentPlayer()->setHandValue(21);

        $loser = $this->cardGame->getLoser();

        $this->assertSame($this->cardGame->player, $loser);
    }
}

