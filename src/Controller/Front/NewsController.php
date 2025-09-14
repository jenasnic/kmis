<?php

namespace App\Controller\Front;

use App\Entity\Content\News;
use App\Repository\Content\NewsRepository;
use App\Service\File\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsController extends AbstractController
{
    #[Route('/actualites', name: 'app_news')]
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('front/news.html.twig', [
            'newsList' => $newsRepository->findOrdered(),
        ]);
    }

    #[Route('/image-actualite/{news}', name: 'app_news_picture')]
    public function picture(FileManager $fileManager, News $news): Response
    {
        $file = $fileManager->download($news, 'pictureUrl');

        if (null === $file) {
            throw $this->createNotFoundException();
        }

        return $this->file($file);
    }
}
