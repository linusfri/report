<?php
use App\Controller\ApiCardController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Kernel;
class ApiCardControllerTest extends WebTestCase
{
    public function testShowDeck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content['cards']);
    }

    public function testShuffle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck/shuffle');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testDraw(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/deck/draw');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content['cardHand']);
        $this->assertIsArray($content['deck']);
    }

    public function testDrawNumber(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/deck/draw/5');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $content = json_decode($response->getContent(), true);

        $this->assertIsArray($content['cardHand']);
        $this->assertCount(5, $content['cardHand']);

        $this->assertIsArray($content['deck']);
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