<?php
use App\Kernel;
use App\Player\Player;
use App\Player\PokerDealer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ApiPokerControllerTest extends WebTestCase
{
    public function testChangeCards(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/proj/game');
        $payload = [1, 3];
        $client->request('POST', '/proj/game/api/changeCards', content: json_encode($payload));

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetCurrentRound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/api/current-round');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetPreviousAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/api/previous-action');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetPlayerName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/api/get-player-name');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsString($responseData);
    }

    public function testGetDealerName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/api/get-dealer-name');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsString($responseData);
    }

    public function testGetCurrentPlayer(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/api/get-current-player');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('handValue', $responseData);
        $this->assertArrayHasKey('isFinished', $responseData);
        $this->assertArrayHasKey('cards', $responseData);
    }

    public function testGetCurrentOpponent(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/api/get-current-opponent');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('handValue', $responseData);
        $this->assertArrayHasKey('isFinished', $responseData);
        $this->assertArrayHasKey('cards', $responseData);
    }

    public function testSetCurrentPlayerMoney(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/proj/game');
        $client->request('POST', '/proj/game/api/set-current-player-money', ['money' => 200]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $responseData['current_player_money']);
    }

    /**
     * createKernel
     *
     * @param array<mixed> $options
     * @return KernelInterface
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new Kernel('test', true);
    }
}
