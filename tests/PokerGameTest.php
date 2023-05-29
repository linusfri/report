<?php

use PHPUnit\Framework\TestCase;
use App\Card\DeckOfCards;
use App\Player\PokerDealer;
use App\Player\Player;
use App\PokerGame\PokerGame;

class PokerGameTest extends TestCase
{
    public Player $player;
    public PokerDealer $dealer;
    public DeckOfCards $deck;
    public PokerGame $game;

    public function setUp(): void {
        $this->dealer = new PokerDealer('Dealer', money: 100);
        $this->player = new Player('Player', money: 100);
        $this->deck = new DeckOfCards();
        $this->game = new PokerGame($this->dealer, $this->player, $this->deck);
    }

    public function testPokerStart()
    {
        // Assert initial game state
        $this->assertEquals($this->player, $this->game->getCurrentPlayer());
        $this->assertEquals($this->dealer, $this->game->getCurrentOpponent());
        $this->assertEquals(1, $this->game->getCurrentRound());
        $this->assertFalse($this->game->isGameOver());
        $this->assertFalse($this->game->getIsShowDown());
    }

    public function testDealCards()
    {
        /** Assert both player and dealer received 5 cards */
        $this->assertCount(5, $this->game->player->getCards());
        $this->assertCount(5, $this->game->dealer->getCards());
    }

    public function testCurrentPlayerFold()
    {
        $this->game->currentPlayerFold();

        /** Assert player has folded */
        $this->assertTrue($this->player->getHasFolded());
        /** Assert game state is updated */
        $this->assertEquals($this->dealer, $this->game->getCurrentPlayer());
    }

    public function testCurrentPlayerRaise()
    {
        $this->game->currentPlayerRaise(20);

        /** Assert bet has been made */
        $this->assertTrue($this->game->getBetHasBeenMade());
        /** Assert current bet is updated */
        $this->assertEquals(20, $this->game->getcurrentBet());
        /** Assert player has played the round */
        $this->assertTrue($this->player->getHasPlayedRound());
        /** Assert game state is updated */
        $this->assertEquals($this->dealer, $this->game->getCurrentPlayer());
    }

    public function testCurrentPlayerCall()
    {
        $this->game->currentPlayerCall();

        /** Assert bet has been made */
        $this->assertTrue($this->game->getBetHasBeenMade());
        /** Assert current bet is updated */
        $this->assertEquals(10, $this->game->getcurrentBet());
        /** Assert player has played the round */
        $this->assertTrue($this->player->getHasPlayedRound());
        /** Assert game state is updated */
        $this->assertEquals($this->dealer, $this->game->getCurrentPlayer());
    }

    public function testCurrentPlayerCheck()
    {
        $this->game->currentPlayerCheck();

        /** Assert player has played the round */
        $this->assertTrue($this->player->getHasPlayedRound());
        /** Assert game state is updated */
        $this->assertEquals($this->dealer, $this->game->getCurrentPlayer());
    }

    public function testDealerEmulateTurn()
    {
        $this->game->nextPlayer();
        $this->game->dealerEmulateTurn();

        /** Assert game state is updated */
        $this->assertEquals($this->player, $this->game->getCurrentPlayer());
    }

    public function testDealerEmulateTurnWhenNotDealer()
    {
        /** Assert exception when not dealer turn */
        $this->expectException(Exception::class);
        $this->game->dealerEmulateTurn();
    }

    public function testGetPokerWinner()
    {
        /** Test when one player folds */
        $this->game->player->fold();
        $this->assertEquals($this->dealer, $this->game->getPokerWinner());

        /** Test when dealer folds */
        $this->game = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        $this->game->dealer->fold();
        $this->assertEquals($this->game->player, $this->game->getPokerWinner());

        /** 
         * Test when both players have money less than current bet and both are have played round.
         * Should not be able to happen in real game. But testing that it returns null in that case.
         * Instantiating a new PokerGame to reset the game state.
         * */
        $this->game = new PokerGame(new PokerDealer('Dealer', money: 100), new Player('Player', money: 100), new DeckOfCards());
        $this->game->player->setMoney(5);
        $this->game->dealer->setMoney(5);
        $this->game->setcurrentBet(10);

        $this->game->dealer->setHasPlayedRound();
        $this->game->player->setHasPlayedRound();

        $this->assertNull($this->game->getPokerWinner());

        /** 
         * Dont test who wins with reference to their current hand.
         * This is random as each player are dealt random cards.
         * These test are not deterministic. Please see PokerHandEvaluatorTest.php for hand evaluation tests.
         * */
    }

    public function testCurrentPlayerChangeCards()
    {
        $this->game->currentPlayerChangeCards([1, 3]);

        /** Assert player has changed cards */
        $this->assertTrue($this->player->getHasChangedCards());
        /** Assert game state is updated */
        $this->assertEquals($this->dealer, $this->game->getCurrentPlayer());
    }

    public function testCurrentPlayerDoneChangeCards()
    {
        $this->game->currentPlayerDoneChangeCards();

        /** Assert player has changed cards */
        $this->assertTrue($this->player->getHasChangedCards());
        /** Assert game state is updated */
        $this->assertEquals($this->dealer, $this->game->getCurrentPlayer());
    }
}
