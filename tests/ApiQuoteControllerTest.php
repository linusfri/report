<?php

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** 
 * TODO
 * Write tests for database, no time right now
 */
class ApiQuoteControllerTest extends WebTestCase
{
    public function testGetQuotes(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/quote');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new \App\Kernel('test', true);
    }
}