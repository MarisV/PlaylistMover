<?php

namespace App\Controller\UserProfile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    #[Route('/profile/transfer', name: 'app_transfer')]
    public function index(): Response
    {
        return $this->render('profile/transfer/index.html.twig', [
            'controller_name' => 'TransferController',
        ]);
    }
}
