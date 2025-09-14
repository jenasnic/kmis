<?php

namespace App\Controller\Front;

use App\Entity\Content\Sporting;
use App\Repository\Content\SportingRepository;
use App\Service\Provider\ScheduleProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SportingController extends AbstractController
{
    #[Route('/disciplines', name: 'app_sporting')]
    public function index(
        SportingRepository $sportingRepository,
        ScheduleProvider $scheduleProvider,
    ): Response {
        return $this->render('front/sporting.html.twig', [
            'sportings' => $sportingRepository->findOrdered(),
            'schedules' => $scheduleProvider->forSporting(),
        ]);
    }

    #[Route('/image-discipline/{sporting}', name: 'app_sporting_picture')]
    public function picture(Sporting $sporting): Response
    {
        if (null === $sporting->getPictureUrl()) {
            throw $this->createNotFoundException();
        }

        return $this->file($sporting->getPictureUrl());
    }
}
