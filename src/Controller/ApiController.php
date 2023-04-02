<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;

class ApiController {
    private $apiClient;
    
    public function __construct() {
        $this->apiClient = HttpClient::create();
    }
    
    #[Route('/api/quote')]
    public function getQuotes(): JsonResponse {
        $response = $this->apiClient->request('GET', 'https://type.fit/api/quotes');
        $quoteArray = $response->toArray();

        $randomResponse = $quoteArray[array_rand($quoteArray)];
        return new JsonResponse($randomResponse);
    }
}