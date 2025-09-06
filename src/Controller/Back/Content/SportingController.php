<?php

namespace App\Controller\Back\Content;

use App\Domain\Command\Back\Content\SaveSportingCommand;
use App\Domain\Command\Back\Content\SaveSportingHandler;
use App\Entity\Content\Sporting;
use App\Form\Content\ManagedListType;
use App\Form\Content\SportingType;
use App\Repository\Content\SportingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SportingController extends AbstractController
{
    public function __construct(
        private readonly SportingRepository $sportingRepository,
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/discipline/liste', name: 'bo_sporting_list', methods: ['GET', 'POST'])]
    public function list(Request $request): Response
    {
        $sportingList = $this->sportingRepository->findAllOrdered();

        $form = $this->createForm(ManagedListType::class, $sportingList, [
            'entityClass' => Sporting::class,
            'withActive' => true,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->entityManager->flush();

            $this->addFlash('info', $this->translator->trans('back.sporting.list.order.success'));

            return $this->redirectToRoute('bo_sporting_list');
        }

        return $this->render('back/content/sporting/list.html.twig', [
            'form' => $form->createView(),
            'sportingCount' => count($sportingList),
        ]);
    }

    #[Route('/discipline/nouvelle-discipline', name: 'bo_sporting_new', methods: ['GET', 'POST'])]
    public function add(Request $request, SaveSportingHandler $saveSportingHandler): Response
    {
        $sporting = new Sporting();

        $sporting->setRank($this->sportingRepository->getFirstRank() - 1);

        return $this->edit($request, $saveSportingHandler, $sporting);
    }

    #[Route('/discipline/modifier/{sporting}', name: 'bo_sporting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SaveSportingHandler $saveSportingHandler, Sporting $sporting): Response
    {
        $form = $this->createForm(SportingType::class, $sporting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saveSportingHandler->handle(new SaveSportingCommand($sporting));

            $this->addFlash('info', $this->translator->trans('back.sporting.edit.success'));

            return $this->redirectToRoute('bo_sporting_list');
        }

        return $this->render('back/content/sporting/edit.html.twig', [
            'form' => $form->createView(),
            'sporting' => $sporting,
        ]);
    }

    #[Route('/discipline/supprimer/{sporting}', name: 'bo_sporting_delete', methods: ['POST'])]
    public function delete(Request $request, Sporting $sporting): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$sporting->getId(), (string) $request->request->get('_token'))) {
            $this->sportingRepository->remove($sporting, true);

            $this->addFlash('info', $this->translator->trans('back.sporting.delete.success'));
        }

        return $this->redirectToRoute('bo_sporting_list', [], Response::HTTP_SEE_OTHER);
    }
}
