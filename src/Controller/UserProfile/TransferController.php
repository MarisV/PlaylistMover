<?php

namespace App\Controller\UserProfile;
use App\Import\PlaylistCreateService;
use App\Service\Enums\Providers;
use App\Service\Fetcher\FetcherFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

#[Route('/profile/transfer', 'app_')]

class TransferController extends AbstractController
{
    public function __construct(
        private readonly Stopwatch $stopwatch
    ) { }

    #[Route('/', name: 'transfer')]
    public function index(): Response
    {
        return $this->render('profile/transfer/index.html.twig', [
            'controller_name' => 'TransferController',
        ]);
    }

    #[Route('/fetch/{provider}', name: 'fetch_playlists')]
    public function fetchPlaylists(Providers $provider, FetcherFactory $fetcherFactory, PlaylistCreateService $playlistCreateService): JsonResponse
    {
        $fetcher = $fetcherFactory->factory($provider);

        $this->stopwatch->start($provider->value, 'Fetch playlists');

        $response = $fetcher->fetchPlaylistsData();

        $this->stopwatch->stop($provider->value);

        $result = $playlistCreateService->createFromApi($response, $provider, $this->getUser());

        return new JsonResponse([
            'status' => 200,
            'data' => $result,
        ]);
    }
}
