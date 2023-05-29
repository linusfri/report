<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LuckyControllerTest extends WebTestCase {
    function testNumber() {
        $client = static::createClient();
        $client->request('GET', '/lucky/number/twig');
        $reponse = $client->getResponse();
        $this->assertSame(200, $reponse->getStatusCode());
        $this->assertSelectorTextContains('title', 'Magic Number');
    }

    function testAbout() {
        $client = static::createClient();
        $client->request('GET', '/about');
        $reponse = $client->getResponse();
        $this->assertSame(200, $reponse->getStatusCode());
        $this->assertSelectorTextContains('title', 'About');
    }
}