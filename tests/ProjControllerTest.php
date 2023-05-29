<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjControllerTest extends WebTestCase
{
    public function testProjRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj');

        $this->assertResponseIsSuccessful();
    }

    public function testPokerAboutRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/about');

        $this->assertResponseIsSuccessful();
    }
}