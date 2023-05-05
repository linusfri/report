<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', 'library')]
    public function library(): Response
    {
        return $this->render('library/library.html.twig');
    }

    #[Route('/library/create', 'create_book', methods: ['POST', 'GET'])]
    public function createBook(BookRepository $bookRepo, Request $req): Response
    {
        if ('GET' == $req->getMethod()) {
            return $this->render('library/create_book.html.twig');
        }

        $postData = $req->request->all();

        $book = new Book();
        $book->setTitle($postData['title']);
        $book->setIsbn($postData['isbn']);
        $book->setAuthor($postData['author']);
        $book->setImg($postData['img']);

        $bookRepo->save($book, flush: true);

        return $this->redirectToRoute('books');
    }

    #[Route('/library/single', 'single_book', methods: ['GET'])]
    public function singleBook(BookRepository $bookRepo, Request $req): Response
    {
        $bookId = $req->query->get('id');

        $book = $bookRepo->find($bookId);

        return $this->render('library/single_book.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/library/books', 'books', methods: ['GET'])]
    public function manyBooks(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findAll();

        return $this->render('library/books.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/library/edit', 'edit_book', methods: ['GET', 'POST'])]
    public function editBook(BookRepository $bookRepo, Request $req): Response
    {
        if ('GET' == $req->getMethod()) {
            $bookId = $req->query->get('id');

            $book = $bookRepo->find($bookId);

            return $this->render('library/edit_book.html.twig', [
                'book' => $book,
            ]);
        }

        $postData = $req->request->all();

        $bookId = $postData['id'];

        $book = $bookRepo->find($bookId);

        $book->setTitle($postData['title']);
        $book->setIsbn($postData['isbn']);
        $book->setAuthor($postData['author']);
        $book->setImg($postData['img']);

        $bookRepo->save($book, flush: true);

        return $this->redirectToRoute('books');
    }

    #[Route('/library/delete', 'delete_book', methods: ['POST'])]
    public function deleteBook(BookRepository $bookRepo, Request $req): Response
    {
        $bookId = $req->request->get('id');
        $book = $bookRepo->find($bookId);

        if (!$book) {
            return $this->redirectToRoute('books');
        }

        $bookRepo->remove($book, flush: true);

        return $this->redirectToRoute('books');
    }
}
