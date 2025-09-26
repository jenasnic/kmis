<?php

namespace App\Service\Export;

use App\Entity\Payment\AbstractPayment;
use App\Entity\Payment\AncvPayment;
use App\Entity\Payment\CheckPayment;
use App\Entity\Payment\RefundHelpPayment;
use App\Entity\Payment\TransferPayment;
use App\Entity\Season;
use App\Repository\Payment\PaymentRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentCsvExport extends AbstractCsvExport
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PaymentRepository $paymentRepository,
    ) {
    }

    public function export(Season $season): StreamedResponse
    {
        /** @var int $seasonId */
        $seasonId = $season->getId();
        $result = $this->paymentRepository->findForExport($seasonId);

        return $this->getStreamedResponse($result);
    }

    /**
     * @return array<string>
     */
    protected function getHeaders(): array
    {
        return [
            'Date',
            'Type',
            'Montant',
            'Adhérent',
            'N° ANCV',
            'N° Chèque',
            'Remise (type + référence)',
            'Libellé virement',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function buildLine(mixed $data): array
    {
        if (!$data instanceof AbstractPayment) {
            throw new \LogicException('invalid data');
        }

        /** @var float $amount */
        $amount = $data->getAmount();

        /** @var array<int, string> $line */
        $line = [
            $data->getDate()->format('d/m/Y'),
            $data->getPaymentType()->trans($this->translator),
            number_format($amount, 2, ',', ' '),
            $data->getAdherent()->getFullName(),
            ($data instanceof AncvPayment) ? $data->getNumber() : '',
            ($data instanceof CheckPayment) ? $data->getNumber() : '',
            ($data instanceof RefundHelpPayment) ? $this->buildRefundHelpMethodReference($data) : '',
            ($data instanceof TransferPayment) ? $data->getLabel() : '',
        ];

        return $line;
    }

    protected function getFilename(): string
    {
        return 'liste_paiements_kmis.csv';
    }

    private function buildRefundHelpMethodReference(RefundHelpPayment $refundHelpPayment): string
    {
        $type = $refundHelpPayment->getRefundHelp()?->trans($this->translator);
        if (null === $type) {
            $type = '???';
        }

        return empty($refundHelpPayment->getReference())
            ? $type
            : sprintf('%s - %s', $type, $refundHelpPayment->getReference())
        ;
    }
}
