<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Kernel;
class PokerControllerTest extends WebTestCase
{

    public function testPokerEndpoint()
    {
        $client = static::createClient();
        $client->request('GET', '/proj/game');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testBetEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/raise?bet=100');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCheckEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/check');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testFoldEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/fold');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCallEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/call');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDoneChangeEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/done-change');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowdownEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/showdown');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGameOverEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/game/game_over');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetSessionEndpoint()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/game');
        $client->request('GET', '/proj/reset');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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
