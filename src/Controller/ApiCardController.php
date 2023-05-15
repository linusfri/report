<?php

namespace App\Controller;

use App\Card\CardHand;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiCardController extends AbstractController
{
    #[Route('api/deck', 'api/deck')]
    public function showDeck(): JsonResponse // ;);)
    {
        $deck = new DeckOfCards();

        return new JsonResponse(['cards' => $deck->cards], headers: [
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
}
