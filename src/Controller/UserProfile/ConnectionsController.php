<?php

namespace App\Controller\UserProfile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnectionsController extends AbstractController
{
    #[Route('/profile/connections/add', name: 'app_connections')]
    public function add(): Response
    {
        return $this->render('profile/connections/index.html.twig', [
            'controller_name' => 'ConnectionsController',
        ]);
    }
}
