<?php

namespace App\Controller\UserProfile;

use App\Service\Fetcher\Interface\FetcherInterface;
use App\Service\Fetcher\SpotifyPlaylistsFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/transfer', 'app_')]

class TransferController extends AbstractController
{   
    public function __construct(
        private FetcherInterface $fetcher,
        private SpotifyPlaylistsFetcher $spotifyPlaylistsFetcher
    ) {
        
    }

    #[Route('/', name: 'transfer')]
    public function index(Request $request): Response
    {
        return $this->render('profile/transfer/index.html.twig', [
            'controller_name' => 'TransferController',
        ]);
    }

    #[Route('/fetch', name: 'fetch_playlists')]
    public function fetchPlaylists()
    {
        $response = $this->spotifyPlaylistsFetcher->fetch();

        return new JsonResponse([
            'status' => 200,
            'data' => $response,
        ]);
    }
}
