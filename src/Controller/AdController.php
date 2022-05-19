<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    #[Route('/{page<\d+>?1}', name: 'ad_index', methods: ['GET'])]
    public function index(int $page): Response
    {
        return $this->render('ad/index.html.twig', []);
    }

    #[Route('view/{pageId<\d+>?1}', name: 'ad_show', methods: ['GET'])]
    public function show(int $pageId): Response
    {
        return $this->render('ad/show.html.twig', [
            'page_id' => $pageId,
        ]);
    }
}