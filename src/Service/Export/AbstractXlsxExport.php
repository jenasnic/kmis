<?php

namespace App\Service\Export;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @phpstan-type _ColumnDataType array{label: string, width: int, align?: string}
 */
abstract class AbstractXlsxExport
{
    public const HEADER_BACKGROUND_COLOR = '8E86AE';
    public const HEADER_TEXT_COLOR = '272B30';
    public const EVEN_ROW_BACKGROUND_COLOR = 'E3E3E3';

    /**
     * @return array<int, _ColumnDataType>
     */
    abstract protected function getColumns(): array;

    abstract protected function buildLine(Worksheet $worksheet, int $rowIndex, mixed $data): void;

    abstract protected function getFilename(): string;

    /**
     * @param \Generator<mixed> $generator
     */
    protected function getFileResponse(string $title, \Generator $generator): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle($title);

        $this->buildHeader($worksheet);

        $rowIndex = 1;
        foreach ($generator as $data) {
            $this->buildLine($worksheet, ++$rowIndex, $data);
        }

        $this->finalizeStyle($worksheet, $rowIndex);

        $tmpFilePath = stream_get_meta_data(tmpfile())['uri'];

        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFilePath);

        $response = new BinaryFileResponse($tmpFilePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->getFilename(),
        );

        return $response;
    }

    protected function buildHeader(Worksheet $worksheet): void
    {
        $columnIndex = 1;
        foreach ($this->getColumns() as $column) {
            $stringColumnIndex = Coordinate::stringFromColumnIndex($columnIndex);
            $worksheet->getColumnDimension($stringColumnIndex)->setWidth($column['width']);
            $worksheet->setCellValue(self::_CELL($columnIndex, 1), $column['label']);

            ++$columnIndex;
        }

        $worksheet->getRowDimension(1)->setRowHeight(30);

        $startColumn = self::_CELL(1, 1);
        $endColumn = self::_CELL($columnIndex - 1, 1);
        $range = Coordinate::buildRange([[$startColumn, $endColumn]]);

        $headerStyle = $worksheet->getStyle($range);
        $this->applyStyle(
            style: $headerStyle,
            bold: true,
            textSize: 12,
            textColor: self::HEADER_TEXT_COLOR,
            backgroundColor: self::HEADER_BACKGROUND_COLOR,
            horizontalCenter: true,
            verticalCenter: true,
        );
    }

    protected function finalizeStyle(Worksheet $worksheet, int $rowCount): void
    {
        $columnIndex = 0;
        foreach ($this->getColumns() as $column) {
            ++$columnIndex;

            if (!array_key_exists('align', $column)) {
                continue;
            }

            $columnRange = Coordinate::buildRange([[self::_CELL($columnIndex, 2), self::_CELL($columnIndex, $rowCount)]]);
            $worksheet->getStyle($columnRange)->getAlignment()->setHorizontal($column['align']);
        }

        for ($rowIndex = 2; $rowIndex <= $rowCount; ++$rowIndex) {
            if (0 === $rowIndex % 2) {
                continue;
            }

            $rowRange = Coordinate::buildRange([[self::_CELL(1, $rowIndex), self::_CELL($columnIndex, $rowIndex)]]);
            $style = $worksheet->getStyle($rowRange);
            $this->applyStyle(
                style: $style,
                backgroundColor: self::EVEN_ROW_BACKGROUND_COLOR,
            );
        }
    }

    /**
     * Helper method to customize style.
     */
    protected function applyStyle(
        Style $style,
        bool $bold = false,
        bool $italic = false,
        ?int $textSize = null,
        ?string $textColor = null,
        ?string $backgroundColor = null,
        bool $wrapText = false,
        bool $horizontalCenter = false,
        bool $verticalCenter = false,
        bool $withBorder = false,
        ?string $borderColor = null,
    ): void {
        if ($bold) {
            $style->getFont()->setBold(true);
        }

        if ($italic) {
            $style->getFont()->setItalic(true);
        }

        if (null !== $textSize) {
            $style->getFont()->setSize($textSize);
        }

        if (null !== $textColor) {
            $style->getFont()->setColor(new Color($textColor));
        }

        if (null !== $backgroundColor) {
            $style->getFill()->setFillType(Fill::FILL_SOLID);
            $style->getFill()->setStartColor(new Color($backgroundColor));
        }

        if ($wrapText) {
            $style->getAlignment()->setWrapText(true);
        }

        if ($horizontalCenter) {
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        if ($verticalCenter) {
            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        if ($withBorder) {
            $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (null !== $borderColor) {
                $style->getBorders()->getAllBorders()->setColor(new Color($borderColor));
            }
        }
    }

    protected static function _CELL(int $columIndex, int $rowIndex): string
    {
        return Coordinate::stringFromColumnIndex($columIndex).$rowIndex;
    }
}
