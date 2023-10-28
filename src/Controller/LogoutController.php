<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function index(): Response
    {
        throw new NotImplementedException('Pass');
    }
}
