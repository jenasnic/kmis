<?php

namespace App\Service\Export;

use App\Entity\Payment\AbstractPayment;
use App\Entity\Payment\AncvPayment;
use App\Entity\Payment\CheckPayment;
use App\Entity\Payment\RefundHelpPayment;
use App\Entity\Payment\TransferPayment;
use App\Entity\Season;
use App\Repository\Payment\PaymentRepository;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentXlsxExport extends AbstractXlsxExport
{
    private ?Season $season = null;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PaymentRepository $paymentRepository,
    ) {
    }

    public function export(Season $season): BinaryFileResponse
    {
        $this->season = $season;

        /** @var int $seasonId */
        $seasonId = $season->getId();
        $result = $this->paymentRepository->findForExport($seasonId);

        return $this->getFileResponse(sprintf('Paiements %s', $season->getDisplayLabel()), $result);
    }

    protected function getColumns(): array
    {
        return [
            ['label' => 'Date', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Type', 'width' => 20],
            ['label' => 'Montant', 'width' => 15, 'align' => Alignment::HORIZONTAL_RIGHT],
            ['label' => 'Adhérent', 'width' => 30],
            ['label' => 'N° ANCV', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'N° Chèque', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Libellé virement', 'width' => 20],
            ['label' => 'Remise (type + référence)', 'width' => 40],
        ];
    }

    protected function buildLine(Worksheet $worksheet, int $rowIndex, mixed $data): void
    {
        if (!$data instanceof AbstractPayment) {
            throw new \LogicException('invalid data');
        }

        /** @var float $amount */
        $amount = $data->getAmount();

        $columnIndex = 1;
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getDate()->format('d/m/Y'));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getPaymentType()->trans($this->translator));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), number_format($amount, 2, ',', ' '));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getFullName());

        $ancvCell = self::_CELL($columnIndex++, $rowIndex);
        $worksheet->setCellValue($ancvCell, ($data instanceof AncvPayment) ? $data->getNumber() : '');
        $this->applyStyle(
            style: $worksheet->getStyle($ancvCell),
            wrapText: true,
        );

        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), ($data instanceof CheckPayment) ? $data->getNumber() : '');
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), ($data instanceof TransferPayment) ? $data->getLabel() : '');
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), ($data instanceof RefundHelpPayment) ? $this->buildRefundHelpMethodReference($data) : '');

        $worksheet->getRowDimension($rowIndex)->setRowHeight(15);
    }

    protected function getFilename(): string
    {
        if (null !== $this->season) {
            return sprintf('liste_paiements_kmis_%s.xlsx', $this->season->getDisplayLabel());
        }

        return 'liste_paiements_kmis.xlsx';
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
