<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Card\Card;
use App\Card\CardHand;
use App\Card\DeckOfCards;

class CardController extends AbstractController
{
    #[Route('/card', 'card')]
    public function Main(): Response
    {
        $data = [
            'routeUrls' => [
                'card/deck',
                'card/deck/shuffle',
                'card/deck/draw',
                'card/deck/draw/5'
            ],
        ];


        return $this->render('card.html.twig', $data);
    }

    #[Route('/card/deck', 'deck')]
    public function ShowDeck(): Response // ;);)
    {
        $deck = new DeckOfCards();

        $data = [
            'deck' => $deck->cards
        ];
        return $this->render('deck.html.twig', $data);
    }

    #[Route('/card/deck/shuffle', 'shuffle')]
    public function Shuffle(): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffleCards();

        $data = [
            'deck' => $deck->cards
        ];
        return $this->render('shuffle.html.twig', $data);
    }

    #[Route('/card/deck/draw', name: 'draw', methods: ['GET'])]
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

        return $this->render('draw.html.twig', $data);
    }

    #[Route('/card/deck/draw/{number<\d+>}', name: 'drawNumber', methods: ['GET'])]
    public function DrawNumber(int $number, SessionInterface $session): Response
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

        return $this->render('draw.html.twig', $data);
    }

    #[Route('/card/deck/reset')]
    public function Reset(Request $req, SessionInterface $session): Response
    {
        if ($req->request->get('reset')) {
            $session->remove('cardHand');
            $session->remove('deck');
        }

        return $this->redirectToRoute('draw');
    }
}
