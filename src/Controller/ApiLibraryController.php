<?php

namespace App\Controller;

use App\Repository\BookRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiLibraryController extends AbstractController
{
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
