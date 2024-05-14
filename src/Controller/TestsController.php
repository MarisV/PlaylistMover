<?php

namespace App\Controller;

use App\Repository\UserOAuthRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    #[Route('/tests', name: 'app_tests')]
    public function index(ParameterBagInterface $bag): Response
    {

        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
        ]);
    }
}
