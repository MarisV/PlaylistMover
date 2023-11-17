<?php

namespace App\Controller\UserProfile;

use App\Repository\UserOAuthRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserOAuthRepository $userOAuthRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'tab' => 'settings',
            'connections' => $userOAuthRepository->findByUser($this->getUser()),
        ]);
    }
}