<?php

namespace App\Controller\Back\Content;

use App\Domain\Command\Back\Content\SaveDiscountCodeConfigurationCommand;
use App\Domain\Command\Back\Content\SaveDiscountCodeConfigurationHandler;
use App\Domain\Model\Content\DiscountCodeConfiguration;
use App\Entity\Payment\DiscountCode;
use App\Enum\RefundHelpEnum;
use App\Form\Content\DiscountCodeConfigurationType;
use App\Repository\Payment\DiscountCodeRepository;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountCodeController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RefundHelpManager $refundHelpManager,
        private readonly DiscountCodeRepository $discountCodeRepository,
        private readonly SaveDiscountCodeConfigurationHandler $saveDiscountCodeConfigurationHandler,
    ) {
    }

    #[Route('/code-reduction/{auto}', name: 'bo_discount_code', methods: ['GET', 'POST'])]
    public function discount(Request $request, bool $auto = false): Response
    {
        $discountCodes = $this->discountCodeRepository->findOrdered();
        $discountCodeConfiguration = $auto
            ? $this->generatePrefFilledConfiguration()
            : new DiscountCodeConfiguration($discountCodes)
        ;

        $form = $this->createForm(DiscountCodeConfigurationType::class, $discountCodeConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->saveDiscountCodeConfigurationHandler->handle(
                new SaveDiscountCodeConfigurationCommand($discountCodeConfiguration)
            );

            $this->addFlash('info', $this->translator->trans('back.discountCodeConfiguration.save.success'));

            return $this->redirectToRoute('bo_discount_code');
        }

        return $this->render('back/content/discount_code/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generatePrefFilledConfiguration(): DiscountCodeConfiguration
    {
        $refundHelpConfiguration = $this->refundHelpManager->getRefundHelpConfiguration();

        $values = [];
        if ($refundHelpConfiguration->passCitizenEnable) {
            $values[] = RefundHelpEnum::PASS_CITIZEN;
        }
        if ($refundHelpConfiguration->passSportEnable) {
            $values[] = RefundHelpEnum::PASS_SPORT;
        }
        if ($refundHelpConfiguration->ccasEnable) {
            $values[] = RefundHelpEnum::CCAS;
        }

        $combinations = $this->getSortedCombinations($values);

        $discountCodes = array_map(fn (array $refundHelps) => DiscountCode::create('', $refundHelps), $combinations);

        return new DiscountCodeConfiguration($discountCodes);
    }

    /**
     * @param array<RefundHelpEnum> $values
     *
     * @return array<array<RefundHelpEnum>>
     */
    private function getSortedCombinations(array $values): array
    {
        $result = [];

        $this->buildCombinationsRecursively($values, $result);

        usort($result, function ($subSetA, $subSetB) {
            // Sort on sub set size
            if (count($subSetA) !== count($subSetB)) {
                return count($subSetA) - count($subSetB);
            }

            // If same size => sort subset depending on sub values
            return strcmp(
                implode('', array_map(fn (RefundHelpEnum $enum) => $enum->value, $subSetA)),
                implode('', array_map(fn (RefundHelpEnum $enum) => $enum->value, $subSetB))
            );
        });

        return $result;
    }

    /**
     * @param array<RefundHelpEnum> $values
     * @param array<array<RefundHelpEnum>> $result
     * @param array<RefundHelpEnum> $subSet
     */
    private function buildCombinationsRecursively(array $values, array &$result, int $index = 0, array $subSet = []): void
    {
        // If end of values is reached
        if ($index >= count($values)) {
            // If set of sub values is not empty => add it to result!
            if (!empty($subSet)) {
                $result[] = $subSet;
            }

            return;
        }

        // 1. Build combinations without current value
        self::buildCombinationsRecursively($values, $result, $index + 1, $subSet);

        // 2. Build combinations with current value
        $subSetWithCurrentValue = array_merge($subSet, [$values[$index]]);
        self::buildCombinationsRecursively($values, $result, $index + 1, $subSetWithCurrentValue);
    }
}
