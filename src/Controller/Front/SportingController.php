<?php

namespace App\Controller\Front;

use App\Entity\Content\Sporting;
use App\Repository\Content\SportingRepository;
use App\Service\File\FileManager;
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
    public function picture(FileManager $fileManager, Sporting $sporting): Response
    {
        $file = $fileManager->download($sporting, 'pictureUrl');

        if (null === $file) {
            throw $this->createNotFoundException();
        }

        return $this->file($file);
    }
}
