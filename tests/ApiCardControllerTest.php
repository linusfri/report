<?php
use App\Controller\ApiCardController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
        // Additional assertions on the deck content
    }

    public function testShuffle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck/shuffle');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $content = json_decode($response->getContent(), true);
        // Additional assertions on the shuffled deck content
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
        // Additional assertions on the drawn cards and the remaining deck content
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
        // Additional assertions on the remaining deck content
    }

}