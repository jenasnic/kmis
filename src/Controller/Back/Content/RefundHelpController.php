<?php

namespace App\Controller\Back\Content;

use App\Form\Content\RefundHelpConfigurationType;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RefundHelpController extends AbstractController
{
    public function __construct(
        private readonly RefundHelpManager $refundHelpManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/aides-remboursement', name: 'bo_refund_help', methods: ['GET', 'POST'])]
    public function refundHelp(Request $request): Response
    {
        $refundHelpConfiguration = $this->refundHelpManager->getRefundHelpConfiguration();

        $form = $this->createForm(RefundHelpConfigurationType::class, $refundHelpConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->refundHelpManager->saveRefundHelpConfiguration($refundHelpConfiguration);

            $this->addFlash('info', $this->translator->trans('back.refundHelp.save.success'));

            return $this->redirectToRoute('bo_refund_help');
        }

        return $this->render('back/content/refund_help/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
