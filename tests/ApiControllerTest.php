<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ApiControllerTest extends WebTestCase
{
    public function testApiRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api');
        $response = $client->getResponse();

        var_dump($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new \App\Kernel('test', true);
    }
}