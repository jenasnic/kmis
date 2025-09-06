<?php

namespace App\Controller\Back\Content;

use App\Entity\Content\Calendar;
use App\Form\Content\CalendarType;
use App\Repository\Content\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CalendarController extends AbstractController
{
    public function __construct(
        private readonly CalendarRepository $calendarRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/planning/liste', name: 'bo_calendar_list', methods: ['GET', 'POST'])]
    public function list(): Response
    {
        return $this->render('back/content/calendar/list.html.twig', [
            'calendars' => $this->calendarRepository->findAllOrdered(),
        ]);
    }

    #[Route('/planning/nouveau-planning', name: 'bo_calendar_new', methods: ['GET', 'POST'])]
    public function add(Request $request): Response
    {
        $calendar = new Calendar();

        return $this->edit($request, $calendar);
    }

    #[Route('/planning/modifier/{calendar}', name: 'bo_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->calendarRepository->add($calendar, true);

            $this->addFlash('info', $this->translator->trans('back.calendar.edit.success'));

            return $this->redirectToRoute('bo_calendar_list');
        }

        return $this->render('back/content/calendar/edit.html.twig', [
            'form' => $form->createView(),
            'calendar' => $calendar,
        ]);
    }

    #[Route('/planning/supprimer/{calendar}', name: 'bo_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar): Response
    {
        if ($this->isCsrfTokenValid('delete-'.$calendar->getId(), (string) $request->request->get('_token'))) {
            $this->calendarRepository->remove($calendar, true);

            $this->addFlash('info', $this->translator->trans('back.calendar.delete.success'));
        }

        return $this->redirectToRoute('bo_calendar_list', [], Response::HTTP_SEE_OTHER);
    }
}
