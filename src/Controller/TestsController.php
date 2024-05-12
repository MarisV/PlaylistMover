<?php

namespace App\Controller;

use App\Repository\UserOAuthRepository;
use App\Service\Enums\Providers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    #[Route('/tests', name: 'app_tests')]
    public function index(UserOAuthRepository $repository): Response
    {

        $auth = $repository->findForTokenRefresh(Providers::SPOTIFY);

        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
        ]);
    }
}
