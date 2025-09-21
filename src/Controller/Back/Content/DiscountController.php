<?php

namespace App\Controller\Back\Content;

use App\Form\Content\DiscountConfigurationType;
use App\Service\Configuration\DiscountManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountController extends AbstractController
{
    public function __construct(
        private readonly DiscountManager $discountManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/code-reduction', name: 'bo_discount', methods: ['GET', 'POST'])]
    public function discount(Request $request): Response
    {
        $discountConfiguration = $this->discountManager->getDiscountConfiguration();

        $form = $this->createForm(DiscountConfigurationType::class, $discountConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->discountManager->saveDiscountConfiguration($discountConfiguration);

            $this->addFlash('info', $this->translator->trans('back.discount.save.success'));

            return $this->redirectToRoute('bo_discount');
        }

        return $this->render('back/content/discount/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
