<?php

namespace App\Services\Excel;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class ExcelExtractor
{
    private const MAX_SHEETS = 20;
    private const MAX_ROWS_PER_SHEET = 5000;

    public function extract(UploadedFile|string $file): array
    {
        if ($file instanceof UploadedFile) {
            Validator::make(
                ['file' => $file],
                [
                    'file' => [
                        'required',
                        'mimes:xlsx,xls'
                    ]
                ]
            )->validate();
        }

        $path = $file instanceof UploadedFile ? $file->getPathname() : (string) $file;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);

        $sheetNames = array_slice($spreadsheet->getSheetNames(), 0, self::MAX_SHEETS);

        $workbook = [];

        foreach ($spreadsheet->getAllSheets() as $sheetIndex => $sheet) {
            if ($sheetIndex >= self::MAX_SHEETS) {
                break;
            }

            $workbook[] = array_slice($sheet->toArray(null, false), 0, self::MAX_ROWS_PER_SHEET);
        }

        $spreadsheet->disconnectWorksheets();

        return $this->parseWorkbook($workbook, $sheetNames);
    }

    public function parseWorkbook(array $workbook, array $sheetNames): array
    {
        $datasets = [];

        foreach ($workbook as $sheetIndex => $rows) {

            if (count($rows) <= 1) {
                continue;
            }

            $sheetName = trim((string) ($sheetNames[$sheetIndex] ?? "Sheet " . ($sheetIndex + 1)));
            if ($sheetName === '') {
                $sheetName = "Sheet " . ($sheetIndex + 1);
            }

            $header = $rows[0];

            $firstHeader = trim((string) ($header[0] ?? "Label"));

            $items = [];

            foreach (array_slice($rows, 1) as $row) {

                $label = trim((string) ($row[0] ?? ""));
                $value = $row[1] ?? null;

                if ($label === "" && $value === null) {
                    continue;
                }

                if (!is_numeric($value)) {
                    continue;
                }

                $items[] = [
                    "label" => $label,
                    "value" => (float) $value,
                ];
            }

            if (empty($items)) {
                continue;
            }

            $datasets[] = [
                "title" => $sheetName,
                "sheet_name" => $sheetName,
                "chart_type" => $this->detectChartType($firstHeader),
                "items" => $items,
            ];
        }

        return $datasets;
    }

    private function detectChartType(string $header): string
    {
        $header = strtolower($header);

        if (str_contains($header, 'tahun') || str_contains($header, 'angkatan')) {
            return 'bar';
        }

        if (str_contains($header, 'bulan') || str_contains($header, 'tanggal')) {
            return 'line';
        }

        if (
            str_contains($header, 'daerah') ||
            str_contains($header, 'provinsi') ||
            str_contains($header, 'agama') ||
            str_contains($header, 'gender')
        ) {
            return 'pie';
        }

        return 'bar';
    }
}