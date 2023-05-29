<?php

namespace App\Controller\Proj;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjController extends AbstractController
{
    #[Route('/proj', 'proj')]
    public function proj(): Response
    {
        return $this->render('proj/home.html.twig');
    }

    #[Route('/proj/about', 'proj/about')]
    public function pokerAbout(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route('/proj/api', 'proj/api')]
    public function projApi(): Response
    {
        return $this->render('proj/api.html.twig');
    }
}
