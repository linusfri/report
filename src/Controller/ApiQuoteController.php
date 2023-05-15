<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiQuoteController extends AbstractController
{
    private HttpClientInterface $apiClient;

    public function __construct()
    {
        $this->apiClient = (new HttpClient())->create();
    }

    #[Route('api/quote', 'quote')]
    public function getQuotes(): JsonResponse
    {
        $response = $this->apiClient->request('GET', 'https://type.fit/api/quotes');
        $quoteArray = $response->toArray();

        $randomResponse = $quoteArray[array_rand($quoteArray)];
        $randomResponse['date'] = date('Y-m-d h:i:s');

        return new JsonResponse($randomResponse);
    }
}
