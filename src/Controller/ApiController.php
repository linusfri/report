<?php

namespace App\Controller;

use App\Card\CardHand;
use App\Card\DeckOfCards;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
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

    #[Route('/api', name: 'api')]
    public function api(): Response
    {
        $data = [
            'routeUrls' => [
                'api/quote',
                'api/deck',
                'api/deck/shuffle',
                'api/deck/draw',
                'api/deck/draw/5',
                'api/game',
                'api/library',
                'api/library/book/9780451524935',
            ],
        ];

        return $this->render('api.html.twig', $data);
    }

    #[Route('api/deck', 'api/deck')]
    public function showDeck(): JsonResponse // ;);)
    {
        $deck = new DeckOfCards();

        return new JsonResponse($deck->cards, headers: [
            'Content-Type' => 'application/json',
        ]);
    }

    #[Route('api/deck/shuffle', 'api/deck/shuffle')]
    public function shuffle(): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffleCards();

        return new JsonResponse($deck);
    }

    #[Route('api/deck/draw', 'api/deck/draw')]
    public function draw(SessionInterface $session): Response
    {
        $cardHand = $session->get('cardHand');
        $deck = $session->get('deck');

        if (!($cardHand && $deck)) {
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
            'deck' => $deck->cards,
        ];

        return new JsonResponse($data);
    }

    #[Route('api/deck/draw/{number<\d+>}', 'api/deck/drawNumber', methods: ['GET'])]
    public function drawNumber(int $number, SessionInterface $session): JsonResponse
    {
        $cardHand = $session->get('cardHand');
        $deck = $session->get('deck');

        if (!($cardHand && $deck)) {
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
            'deck' => $deck->cards,
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/deck/reset', name: '/api/deck/reset')]
    public function reset(SessionInterface $session): Response
    {
        $session->remove('cardHand');
        $session->remove('deck');

        return $this->redirectToRoute('api/deck/drawNumber', ['number' => 1]);
    }

    #[Route('/api/game', 'api_game')]
    public function apiGame(SessionInterface $session): JsonResponse
    {
        $gameSession = $session->get('game') ?? 'No game started yet!';

        return new JsonResponse($gameSession);
    }

    #[Route('/api/library', name: 'api/library')]
    public function library(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findAll();

        return new JsonResponse($books);
    }

    #[Route('/api/library/book/{isbn}', name: 'api/library/book/{isbn}')]
    public function libraryIsbn(string $isbn, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->findOneBy(['isbn' => $isbn]);

        return new JsonResponse($book);
    }
}
