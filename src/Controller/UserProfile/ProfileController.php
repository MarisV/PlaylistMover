<?php

namespace App\Controller\UserProfile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'tab' => 'index'
        ]);
    }

    // #[Route('/profile/connections', name: 'app_profile_connections')]
    // public function connections(): Response
    // {
    //     return $this->render('profile/index.html.twig', [
    //         'tab' => 'connections'
    //     ]);
    // }
}

// 