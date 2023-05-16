<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Kernel;

class ApiGameControllerTest extends WebTestCase
{
    public function testApiGameNotStarted(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/game');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $content = json_decode($response->getContent(), true);
        $this->assertIsString($content['game']);
    }

    public function testApiGameStarted(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/game');
        $client->request('GET', '/api/game');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content['game']);
        $this->assertIsArray($content['game']['currentPlayer']);
        $this->assertIsArray($content['game']['player']);
        $this->assertIsArray($content['game']['dealer']);
        $this->assertFalse($content['game']['gameOver']);
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