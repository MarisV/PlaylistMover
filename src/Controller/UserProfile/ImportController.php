<?php

namespace App\Controller\UserProfile;
use App\Import\PlaylistCreateService;
use App\Repository\PlaylistRepository;
use App\Service\Enums\Providers;
use App\Service\Fetcher\FetcherFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

#[Route('/profile/import', 'app_')]

class ImportController extends AbstractController
{
    public function __construct(private readonly Stopwatch $stopwatch) { }

    #[Route('/', name: 'import')]
    public function index(PlaylistRepository $playlistRepository): Response
    {
        return $this->render('profile/import/index.html.twig', [
           'stats' => $playlistRepository->getUserStats($this->getUser()),
        ]);
    }

    #[Route('/fetch/{provider}', name: 'fetch_playlists')]
    public function fetchPlaylists(
        Providers $provider,
        FetcherFactory $fetcherFactory,
        PlaylistCreateService $playlistCreateService
    ): RedirectResponse {
        $fetcher = $fetcherFactory->factory($provider);

        $this->stopwatch->start('Start');

        $response = $fetcher->fetchPlaylistsData();

        $result = $playlistCreateService->createFromApi($response, $provider, $this->getUser());

        $event = $this->stopwatch->stop('Start');

        $time = $event->getDuration() / 1000;

        $this->addFlash('success', 'Success. Time: ' . $time . ' seconds');

        return $this->redirectToRoute('app_import');
    }
}