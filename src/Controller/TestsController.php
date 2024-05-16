<?php

namespace App\Controller;

use App\Repository\TrackRepository;
use App\Repository\UserOAuthRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    #[Route('/tests', name: 'app_tests')]
    public function index(TrackRepository $trackRepository): Response
    {

        $identifiers = $trackRepository->getIdentifiers();

        dd($identifiers);

        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
        ]);
    }
}
