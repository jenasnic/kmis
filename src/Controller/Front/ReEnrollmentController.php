<?php

namespace App\Controller\Front;

use App\Domain\Command\Front\ReEnrollmentCommand;
use App\Domain\Command\Front\ReEnrollmentHandler;
use App\Domain\Model\ReEnrollment;
use App\Entity\ReEnrollmentToken;
use App\Form\NewRegistrationType;
use App\Form\ReEnrollmentType;
use App\Repository\ReEnrollmentTokenRepository;
use App\Service\File\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReEnrollmentController extends AbstractController
{
    protected const RE_ENROLLMENT_TOKEN = 're_enrollment_token';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly FileManager $fileManager,
        private readonly ReEnrollmentTokenRepository $reEnrollmentTokenRepository,
    ) {
    }

    #[Route('/reinscription/verification/{token}', name: 'app_re_enrollment', methods: ['GET', 'POST'])]
    public function reEnrollment(Request $request, string $token): Response
    {
        $reEnrollment = new ReEnrollment();
        $form = $this->createForm(ReEnrollmentType::class, $reEnrollment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ReEnrollmentToken|null $reEnrollmentToken */
            $reEnrollmentToken = $this->reEnrollmentTokenRepository->find($token);
            if (null === $reEnrollmentToken || $reEnrollmentToken->getAdherent()->getEmail() !== $reEnrollment->email) {
                $this->addFlash('error', $this->translator->trans('front.reEnrollment.error'));

                return $this->redirectToRoute('app_home');
            }

            $this->requestStack->getSession()->set(self::RE_ENROLLMENT_TOKEN, $token);

            return $this->redirectToRoute('app_re_enrollment_update');
        }

        return $this->render('front/re_enrollment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reinscription/mise-a-jour', name: 'app_re_enrollment_update')]
    public function reEnrollmentUpdate(Request $request, ReEnrollmentHandler $reEnrollmentHandler): Response
    {
        $reEnrollmentToken = $this->getReEnrollmentToken();
        $season = $reEnrollmentToken->getSeason();

        if ($reEnrollmentToken->getExpiresAt() < new \DateTime() || !$season->isActive()) {
            $this->addFlash('error', $this->translator->trans('front.reEnrollment.expired'));

            return $this->redirectToRoute('app_home');
        }

        $registration = $reEnrollmentToken->getAdherent()->getRegistration();
        if (null === $registration) {
            $this->addFlash('error', $this->translator->trans('front.reEnrollment.noRegistration'));

            return $this->redirectToRoute('app_home');
        }

        $registration->prepareForReEnrollment($reEnrollmentToken->getSeason());
        $this->fileManager->cleanRegistration($registration);

        $formOptions = ['re_enrollment' => true];

        $isPatch = $request->isMethod(Request::METHOD_PATCH);
        if ($isPatch) {
            $formOptions['method'] = Request::METHOD_PATCH;
            $formOptions['validation_groups'] = false;
        }

        $form = $this->createForm(NewRegistrationType::class, $registration, $formOptions);
        $form->handleRequest($request);

        if (!$isPatch && $form->isSubmitted() && $form->isValid()) {
            $reEnrollmentHandler->handle(new ReEnrollmentCommand($registration, $reEnrollmentToken, true));

            $this->requestStack->getSession()->remove(self::RE_ENROLLMENT_TOKEN);

            $this->addFlash('info', $this->translator->trans('front.reEnrollment.success'));

            return $this->redirectToRoute('app_home');
        }

        return $this->render('front/registration.html.twig', [
            'form' => $form->createView(),
            'registration' => $registration,
            'reEnrollment' => true,
        ]);
    }

    protected function getReEnrollmentToken(): ReEnrollmentToken
    {
        $token = $this->requestStack->getSession()->get(self::RE_ENROLLMENT_TOKEN);

        if (null === $token) {
            throw $this->createNotFoundException('No re-enrollment token found.');
        }

        /** @var ReEnrollmentToken|null $reEnrollmentToken */
        $reEnrollmentToken = $this->reEnrollmentTokenRepository->find($token);

        if (null === $reEnrollmentToken) {
            throw $this->createNotFoundException('No re-enrollment token found.');
        }

        return $reEnrollmentToken;
    }
}
