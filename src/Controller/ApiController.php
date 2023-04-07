<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


use App\Card\Card;
use App\Card\CardHand;
use App\Card\DeckOfCards;

class ApiController extends AbstractController
{
    private $apiClient;

    public function __construct()
    {
        $this->apiClient = HttpClient::create();
    }

    #[Route('api/quote', 'quote')]
    public function getQuotes(): JsonResponse
    {
        $response = $this->apiClient->request('GET', 'https://type.fit/api/quotes');
        $quoteArray = $response->toArray();

        $randomResponse = $quoteArray[array_rand($quoteArray)];
        $randomResponse['date'] = date("Y-m-d h:i:s");
        return new JsonResponse($randomResponse);
    }

    #[Route('/api', name: 'api')]
    public function Api(): Response
    {
        $data = [
            'routeUrls' => [
                'api/quote'
            ]
        ];
        return $this->render('api.html.twig', $data);
    }

    #[Route('api/deck', 'api/deck')]
    public function ShowDeck(): JsonResponse // ;);)
    {
        $deck = new DeckOfCards();

        return new JsonResponse($deck->cards, headers: [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('api/deck/shuffle', 'api/deck/shuffle')]
    public function Shuffle(): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffleCards();

        return new JsonResponse($deck);
    }

    #[Route('api/deck/draw', 'api/deck/draw')]
    public function Draw(SessionInterface $session): Response
    {
        $cardHand = $session->get('cardHand');
        $deck = $session->get('deck');

        if (! ($cardHand && $deck)) {
            $cardHand = new CardHand();
            $deck = new DeckOfCards();

            $deck->shuffleCards();

            $session->set('cardHand', $cardHand);
            $session->set('deck', $deck);
        }

        $cardHand->drawCards($deck);
        $session->set('cardHand', $cardHand);
        $session->set('deck', $deck);

        $data = [
            'cardHand' => $cardHand->cards,
            'deck' => $deck->cards
        ];

        return new JsonResponse($data);
    }

    #[Route('api/deck/draw/{number<\d+>}', 'api/deck/drawNumber', methods: ['GET'])]
    public function DrawNumber(int $number, SessionInterface $session): JsonResponse
    {
        $cardHand = $session->get('cardHand');
        $deck = $session->get('deck');

        if (! ($cardHand && $deck)) {
            $cardHand = new CardHand();
            $deck = new DeckOfCards();

            $deck->shuffleCards();

            $session->set('cardHand', $cardHand);
            $session->set('deck', $deck);
        }

        $cardHand->drawCards($deck, $number);
        $session->set('cardHand', $cardHand);
        $session->set('deck', $deck);

        $data = [
            'cardHand' => $cardHand->cards,
            'deck' => $deck->cards
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/deck/reset', name: '/api/deck/reset')]
    public function Reset(SessionInterface $session): Response
    {
        $session->remove('cardHand');
        $session->remove('deck');

        return $this->redirectToRoute('api/deck/drawNumber', ['number' => 1]);
    }
}
