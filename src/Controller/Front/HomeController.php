<?php

namespace App\Controller\Front;

use App\Repository\Content\LocationRepository;
use App\Repository\Content\SportingRepository;
use App\Repository\SeasonRepository;
use App\Service\Configuration\TextManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        SeasonRepository $seasonRepository,
        SportingRepository $sportingRepository,
        LocationRepository $locationRepository,
        TextManager $textManager,
    ): Response {
        $activeSeason = $seasonRepository->getActiveSeason();
        $priceOptions = $activeSeason ? $activeSeason->getPriceOptions()->toArray() : [];

        return $this->render('front/home.html.twig', [
            'homePresentation' => $textManager->getHomePresentation(),
            'priceOptions' => $priceOptions,
            'sportings' => $sportingRepository->findOrdered(),
            'locations' => $locationRepository->findOrdered(),
        ]);
    }

    #[Route('/protections-des-donnees', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('front/privacy.html.twig');
    }
}
