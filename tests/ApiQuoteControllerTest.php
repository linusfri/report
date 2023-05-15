<?php
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Book;

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
}