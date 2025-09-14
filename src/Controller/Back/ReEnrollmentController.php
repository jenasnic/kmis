<?php

namespace App\Controller\Back;

use App\Domain\Command\Front\ReEnrollmentCommand;
use App\Domain\Command\Front\ReEnrollmentHandler;
use App\Entity\Registration;
use App\Form\RegistrationType;
use App\Repository\ReEnrollmentTokenRepository;
use App\Repository\SeasonRepository;
use App\Service\File\FileManager;
use App\Service\Notifier\ReEnrollmentNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReEnrollmentController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly SeasonRepository $seasonRepository,
        private readonly ReEnrollmentTokenRepository $reEnrollmentTokenRepository,
        private readonly ReEnrollmentNotifier $reEnrollmentNotifier,
        private readonly ReEnrollmentHandler $reEnrollmentHandler,
        private readonly FileManager $fileManager,
        private readonly int $mailerMaxPacketSize,
    ) {
    }

    #[Route('/adherent/re-inscription/{registration}', name: 'bo_re_enrollment', methods: ['GET', 'POST', 'PATCH'])]
    public function reEnrollment(Request $request, Registration $registration): Response
    {
        $season = $this->seasonRepository->getActiveSeason();
        if (null === $season || $season->getId() === $registration->getSeason()->getId()) {
            $this->addFlash('error', $this->translator->trans('back.registration.reEnrollment.error'));

            return $this->redirectToRoute('bo_registration_edit', ['registration' => $registration->getId()]);
        }

        $formOptions = [];

        $isPatch = $request->isMethod(Request::METHOD_PATCH);
        if ($isPatch) {
            $formOptions['method'] = Request::METHOD_PATCH;
            $formOptions['validation_groups'] = false;
        }

        $registration->prepareForReEnrollment($season);
        $this->fileManager->cleanRegistration($registration);

        $form = $this->createForm(RegistrationType::class, $registration, $formOptions);
        $form->handleRequest($request);

        if (!$isPatch && $form->isSubmitted() && $form->isValid()) {
            $this->reEnrollmentHandler->handle(new ReEnrollmentCommand($registration));

            $this->addFlash('info', $this->translator->trans('back.registration.reEnrollment.success'));

            return $this->redirectToRoute('bo_adherent_list');
        }

        return $this->render('back/registration/edit.html.twig', [
            'form' => $form->createView(),
            'registration' => $registration,
            'reEnrollment' => true,
        ]);
    }

    #[Route('/adherent/notifier/re-inscription', name: 'bo_re_enrollment_notify', methods: ['POST'])]
    public function reEnrollmentNotify(Request $request): Response
    {
        if ($this->isCsrfTokenValid('re_enrollment_notify', (string) $request->request->get('_token'))) {
            $emailSentCount = $this->reEnrollmentNotifier->notify($this->mailerMaxPacketSize);

            $this->addFlash('info', $this->translator->trans('back.registration.reEnrollment.notify.message', ['%count%' => $emailSentCount]));
        }

        return $this->redirectToRoute('bo_season_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/adherent/token-re-inscription-expire/supprimer', name: 'bo_re_enrollment_expired_token_clear', methods: ['POST'])]
    public function reEnrollmentExpiredTokenClear(Request $request): Response
    {
        if ($this->isCsrfTokenValid('re_enrollment_expired_token_clear', (string) $request->request->get('_token'))) {
            $this->reEnrollmentTokenRepository->removeExpiredToken();

            $this->addFlash('info', $this->translator->trans('back.registration.reEnrollment.clearToken.message'));
        }

        return $this->redirectToRoute('bo_season_list', [], Response::HTTP_SEE_OTHER);
    }
}
