<?php

namespace App\Controller\Back;

use App\Repository\SeasonRepository;
use App\Service\Provider\StatisticsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly StatisticsProvider $statisticsProvider,
    ) {
    }

    #[Route('/', name: 'bo_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        $season = $this->seasonRepository->getActiveSeason();

        $receipt = $registration = [];

        if (null !== $season) {
            /** @var int $seasonId */
            $seasonId = $season->getId();

            $receipt = $this->statisticsProvider->getReceipt($seasonId);
            $registration = $this->statisticsProvider->getRegistration($seasonId);
        }

        return $this->render('back/dashboard/dashboard.html.twig', [
            'season' => $season,
            'receipt' => $receipt,
            'registration' => $registration,
        ]);
    }
}
