<?php

namespace App\Controller;

use App\Card\CardHand;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    #[Route('/card', 'card')]
    public function main(): Response
    {
        $data = [
            'routeUrls' => [
                'card/deck',
                'card/deck/shuffle',
                'card/deck/draw',
                'card/deck/draw/5',
            ],
        ];

        return $this->render('card.html.twig', $data);
    }

    #[Route('/card/deck', 'deck')]
    public function showDeck(): Response // ;);)
    {
        $deck = new DeckOfCards();

        $data = [
            'deck' => $deck->cards,
        ];

        return $this->render('deck.html.twig', $data);
    }

    #[Route('/card/deck/shuffle', 'shuffle')]
    public function shuffle(): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffleCards();

        $data = [
            'deck' => $deck->cards,
        ];

        return $this->render('shuffle.html.twig', $data);
    }

    #[Route('/card/deck/draw', name: 'draw', methods: ['GET'])]
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

        return $this->render('draw.html.twig', $data);
    }

    #[Route('/card/deck/draw/{number<\d+>}', name: 'drawNumber', methods: ['GET'])]
    public function drawNumber(int $number, SessionInterface $session): Response
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

        return $this->render('draw.html.twig', $data);
    }

    #[Route('/card/deck/reset')]
    public function reset(Request $req, SessionInterface $session): Response
    {
        if ($req->request->get('reset')) {
            $session->remove('cardHand');
            $session->remove('deck');
        }

        return $this->redirectToRoute('draw');
    }
}
