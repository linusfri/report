<?php
use App\Controller\Proj\ApiPokerController;
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

    /**
     * createKernel
     *
     * @param array<mixed> $options
     * @return KernelInterface
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new KernelInterface('test', true);
    }
}
