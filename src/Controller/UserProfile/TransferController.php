<?php

namespace App\Controller\UserProfile;
use App\Import\PlaylistCreateService;
use App\Model\PlaylistsStatsModel;
use App\Service\Enums\Providers;
use App\Service\Fetcher\FetcherFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/transfer', 'app_')]

class TransferController extends AbstractController
{
    public function __construct() { }

    #[Route('/', name: 'transfer')]
    public function index(): Response
    {
        return $this->render('profile/transfer/index.html.twig', [
           'stats' => (new PlaylistsStatsModel($this->getUser()))->getPlaylistsStats(),
        ]);
    }

    #[Route('/fetch/{provider}', name: 'fetch_playlists')]
    public function fetchPlaylists(
        Providers $provider,
        FetcherFactory $fetcherFactory,
        PlaylistCreateService $playlistCreateService
    ): RedirectResponse {
        $fetcher = $fetcherFactory->factory($provider);

        $response = $fetcher->fetchPlaylistsData();

//        $result = $playlistCreateService->createFromApi($response, $provider, $this->getUser());

        $this->addFlash('success', 'Success');

        return $this->redirectToRoute('app_transfer');
    }
}
