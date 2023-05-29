<?php
use App\Helpers\Helper;
use App\Player\PokerBrain;
use App\PokerGame\PokerGame;
use App\Player\Dealer;
use PHPUnit\Framework\TestCase;

class PokerBrainTest extends TestCase
{
    public function testGenerateNewRoundIdeas(): void
    {
        $pokerBrain = new PokerBrain();
        $pokerBrain->generateNewRoundIdeas();

        $ideas = $this->getPrivatePropertyValue($pokerBrain, 'ideas');

        $this->assertIsArray($ideas);
    }


    private function getPrivatePropertyValue(object $object, string $property)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
