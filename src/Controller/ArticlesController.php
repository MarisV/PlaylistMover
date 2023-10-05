<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    #[Route(path: '/articles', name: 'articles', methods: ['GET'])]
    public function list(): Response
    {
        return new Response('Welcome to Latte and Code ');
    }

    #[Route(path: '/lucky/number', name: 'lucky', methods: ['GET'])]
    public function lucky()
    {
            $number = random_int(1, 30);

            return new Response(
                '<html><body>Lucky number: '.$number.'</body></html>'
            );
    }

}
