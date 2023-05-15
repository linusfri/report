<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testApiRoute(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api');
        $content = $crawler->filter('.link-container')->text();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('api/quote', $content);
        $this->assertStringContainsString('api/deck', $content);
        $this->assertStringContainsString('api/deck/shuffle', $content);
        $this->assertStringContainsString('api/deck/draw', $content);
        $this->assertStringContainsString('api/deck/draw/5', $content);
        $this->assertStringContainsString('api/game', $content);
        $this->assertStringContainsString('api/library', $content);
        $this->assertStringContainsString('api/library/book/9780451524935', $content);
    }
}