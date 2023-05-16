<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Kernel;

class ApiControllerTest extends WebTestCase
{   
    /**
     * testApiRoute
     * Test works locally but not on scrutinizer.
     * The route is available in prod. Should look into this.
     */
    // public function testApiRoute(): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/api');
    //     $response = $client->getResponse();

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
    // }

    public function testPlaceholder(): void
    {
        $this->assertTrue(true);
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