<?php

namespace App\Form\DataMapper\Payment;

use App\Entity\Adherent;
use App\Entity\Payment\RefundHelpPayment;
use App\Entity\Season;
use App\Enum\RefundHelpEnum;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormInterface;

class RefundHelpPaymentDataMapper implements DataMapperInterface
{
    public function __construct(
        private readonly RefundHelpManager $refundHelpManager,
        private readonly Adherent $adherent,
        private readonly Season $season,
    ) {
    }

    /**
     * @param RefundHelpPayment|null $viewData
     * @param \Traversable<FormInterface<mixed>> $forms
     */
    public function mapDataToForms($viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof RefundHelpPayment) {
            return;
        }

        $forms = iterator_to_array($forms);

        $forms['refundHelp']->setData($viewData->getRefundHelp());
        $forms['date']->setData($viewData->getDate());
        $forms['comment']->setData($viewData->getComment());
        $forms['reference']->setData($viewData->getReference());
    }

    /**
     * @param \Traversable<FormInterface<mixed>> $forms
     * @param RefundHelpPayment|null $viewData
     */
    public function mapFormsToData(\Traversable $forms, &$viewData): void
    {
        $forms = iterator_to_array($forms);

        try {
            if (null === $viewData) {
                $viewData = new RefundHelpPayment($this->adherent, $this->season);
            }

            /** @var RefundHelpEnum|null $refundHelp */
            $refundHelp = $forms['refundHelp']->getData();
            /** @var \DateTime|null $date */
            $date = $forms['date']->getData();
            /** @var string|null $comment */
            $comment = $forms['comment']->getData();
            /** @var string|null $reference */
            $reference = $forms['reference']->getData();

            $amount = (null !== $refundHelp) ? $this->refundHelpManager->getAmount($refundHelp) : null;

            $viewData->setRefundHelp($refundHelp);
            $viewData->setAmount($amount);
            $viewData->setComment($comment);
            $viewData->setReference($reference);

            if (null !== $date) {
                $viewData->setDate($date);
            }
        } catch (\Exception $e) {
            throw new TransformationFailedException('Unable to map data for refund help payment', 0, $e);
        }
    }
}
