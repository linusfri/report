<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', 'home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/about', 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route('/report', 'report')]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    #[Route('metrics', 'metrics')]
    public function metrics(): Response
    {
        return $this->render('metrics/metrics.html.twig');
    }

    #[Route('/proj', 'proj')]
    public function proj(): Response
    {
        return $this->render('proj/home.html.twig');
    }
}
