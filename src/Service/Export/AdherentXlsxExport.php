<?php

namespace App\Service\Export;

use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdherentXlsxExport extends AbstractXlsxExport
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RegistrationRepository $registrationRepository,
    ) {
    }

    public function export(): BinaryFileResponse
    {
        $result = $this->registrationRepository->findForExport();

        return $this->getFileResponse('Adhérents', $result);
    }

    protected function getColumns(): array
    {
        return [
            ['label' => 'Nom', 'width' => 20],
            ['label' => 'Prénom', 'width' => 20],
            ['label' => 'Représentant légal', 'width' => 30],
            ['label' => 'Contact urgence', 'width' => 45],
            ['label' => 'Pseudo Facebook', 'width' => 20],
            ['label' => 'Date de naissance', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Sexe', 'width' => 10, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'E-mail', 'width' => 30],
            ['label' => 'Téléphone', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Adresse', 'width' => 80],
            ['label' => 'Commentaire', 'width' => 80],
            ['label' => 'Objectif', 'width' => 30],
            ['label' => 'Droit à l\'image', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'N° de licence', 'width' => 20, 'align' => Alignment::HORIZONTAL_RIGHT],
            ['label' => 'Date licence', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Saison', 'width' => 15, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Formule', 'width' => 30],
            ['label' => 'Pass Citoyen', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'Pass Sport', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
            ['label' => 'CCAS', 'width' => 20, 'align' => Alignment::HORIZONTAL_CENTER],
        ];
    }

    protected function buildLine(Worksheet $worksheet, int $rowIndex, mixed $data): void
    {
        if (!$data instanceof Registration) {
            throw new \LogicException('invalid data');
        }

        $legalRepresentative = '';
        if ($data->isWithLegalRepresentative()) {
            $legalRepresentative = sprintf(
                '%s %s',
                $data->getLegalRepresentative()?->getLastName() ?? '',
                $data->getLegalRepresentative()?->getFirstName() ?? '',
            );
        }

        $emergencyContact = sprintf(
            '%s %s [%s]',
            $data->getEmergency()?->getLastName() ?? '',
            $data->getEmergency()?->getFirstName() ?? '',
            $data->getEmergency()?->getPhone() ?? '',
        );

        $columnIndex = 1;
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getLastName());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getFirstName());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $legalRepresentative);
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $emergencyContact);
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getPseudonym());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getBirthDate()?->format('d/m/Y') ?? '');
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getGender()?->trans($this->translator) ?? '');
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getEmail());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getAdherent()->getPhone());

        $addressCell = self::_CELL($columnIndex++, $rowIndex);
        $worksheet->setCellValue($addressCell, $data->getAdherent()->getAddress());
        $this->applyStyle(
            style: $worksheet->getStyle($addressCell),
            wrapText: true,
        );

        $commentCell = self::_CELL($columnIndex++, $rowIndex);
        $worksheet->setCellValue($commentCell, $data->getComment());
        $this->applyStyle(
            style: $worksheet->getStyle($commentCell),
            wrapText: true,
        );

        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getPurpose()?->getLabel());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getCopyrightAuthorization() ? $this->translator->trans('global.yes') : $this->translator->trans('global.no'));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getLicenceNumber());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getLicenceDate()?->format('d/m/Y'));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getSeason()->getDisplayLabel());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->getPriceOption()?->getLabel());
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->isUsePassCitizen() ? $this->translator->trans('global.yes') : $this->translator->trans('global.no'));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->isUsePassSport() ? $this->translator->trans('global.yes') : $this->translator->trans('global.no'));
        $worksheet->setCellValue(self::_CELL($columnIndex++, $rowIndex), $data->isUseCCAS() ? $this->translator->trans('global.yes') : $this->translator->trans('global.no'));

        $worksheet->getRowDimension($rowIndex)->setRowHeight(15);
    }

    protected function getFilename(): string
    {
        return 'liste_adherents_kmis.xlsx';
    }
}
