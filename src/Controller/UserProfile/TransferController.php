<?php

namespace App\Controller\UserProfile;

use App\Repository\UserOAuthRepository;
use App\Service\Enums\Providers;
use App\Service\Fetcher\FetcherFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Clock\now;

#[Route('/profile/transfer', 'app_')]

class TransferController extends AbstractController
{
    public function __construct(
    ) { }

    #[Route('/', name: 'transfer')]
    public function index(Request $request, UserOAuthRepository $userOAuthRepository): Response
    {




        return $this->render('profile/transfer/index.html.twig', [
            'controller_name' => 'TransferController',
        ]);
    }

    #[Route('/fetch/{provider}', name: 'fetch_playlists')]
    public function fetchPlaylists(Providers $provider, FetcherFactory $fetcherFactory): JsonResponse
    {
        $fetcher = $fetcherFactory->factory($provider);

        $response = $fetcher->fetchPlaylists();

        return new JsonResponse([
            'status' => 200,
            'data' => $response,
        ]);
    }
}
