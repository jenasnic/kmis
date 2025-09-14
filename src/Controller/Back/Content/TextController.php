<?php

namespace App\Controller\Back\Content;

use App\Domain\Model\Content\ConfigurationText;
use App\Form\Content\ConfigurationTextType;
use App\Repository\ConfigurationRepository;
use App\Service\Configuration\TextManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TextController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationRepository $configurationRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/contenus', name: 'bo_text', methods: ['GET', 'POST'])]
    public function text(Request $request): Response
    {
        $configurationText = new ConfigurationText(
            $this->configurationRepository->getOrCreate(TextManager::HOME_PRESENTATION),
            $this->configurationRepository->getOrCreate(TextManager::CONTACT),
        );

        $form = $this->createForm(ConfigurationTextType::class, $configurationText);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->entityManager->persist($configurationText->homePresentation);
            $this->entityManager->persist($configurationText->contact);
            $this->entityManager->flush();

            $this->addFlash('info', $this->translator->trans('back.text.save.success'));

            return $this->redirectToRoute('bo_text');
        }

        return $this->render('back/content/text/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
