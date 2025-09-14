<?php

namespace App\Controller\Back\Content;

use App\Entity\Content\Location;
use App\Form\Content\LocationType;
use App\Form\Content\ManagedListType;
use App\Repository\Content\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocationController extends AbstractController
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/localisation/liste', name: 'bo_location_list', methods: ['GET', 'POST'])]
    public function list(Request $request): Response
    {
        $locationList = $this->locationRepository->findAllOrdered();

        $form = $this->createForm(ManagedListType::class, $locationList, [
            'entityClass' => Location::class,
            'withActive' => true,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->entityManager->flush();

            $this->addFlash('info', $this->translator->trans('back.location.list.order.success'));

            return $this->redirectToRoute('bo_location_list');
        }

        return $this->render('back/content/location/list.html.twig', [
            'form' => $form->createView(),
            'locationCount' => count($locationList),
        ]);
    }

    #[Route('/localisation/nouvelle-localisation', name: 'bo_location_new', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $location = new Location();

        $location->setRank($this->locationRepository->getFirstRank() - 1);

        return $this->edit($request, $location);
    }

    #[Route('/localisation/modifier/{location}', name: 'bo_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Location $location): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->locationRepository->add($location, true);

            $this->addFlash('info', $this->translator->trans('back.location.edit.success'));

            return $this->redirectToRoute('bo_location_list');
        }

        return $this->render('back/content/location/edit.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    #[Route('/localisation/supprimer/{location}', name: 'bo_location_delete', methods: ['POST'])]
    public function delete(Request $request, Location $location): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$location->getId(), (string) $request->request->get('_token'))) {
            $this->locationRepository->remove($location, true);

            $this->addFlash('info', $this->translator->trans('back.location.delete.success'));
        }

        return $this->redirectToRoute('bo_location_list', [], Response::HTTP_SEE_OTHER);
    }
}
