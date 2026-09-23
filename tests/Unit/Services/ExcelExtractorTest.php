<?php

namespace Tests\Unit\Services;

use App\Services\Excel\ExcelExtractor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelExtractorTest extends TestCase
{
    private ExcelExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new ExcelExtractor;
    }

    public function test_parse_workbook_maps_tahun_header_to_bar(): void
    {
        $workbook = [
            [['Tahun', 'Jumlah'], ['2023', 100], ['2024', 120]],
        ];

        $result = $this->extractor->parseWorkbook($workbook, ['Mahasiswa']);

        $this->assertCount(1, $result);
        $this->assertSame('Mahasiswa', $result[0]['title']);
        $this->assertSame('Mahasiswa', $result[0]['sheet_name']);
        $this->assertSame('bar', $result[0]['chart_type']);
        $this->assertSame(
            [['label' => '2023', 'value' => 100.0], ['label' => '2024', 'value' => 120.0]],
            $result[0]['items']
        );
    }

    public function test_parse_workbook_maps_bulan_header_to_line(): void
    {
        $workbook = [
            [['Bulan', 'Total'], ['Januari', 10], ['Februari', 20]],
        ];

        $result = $this->extractor->parseWorkbook($workbook, ['Tren']);

        $this->assertSame('line', $result[0]['chart_type']);
    }

    public function test_parse_workbook_maps_daerah_header_to_pie(): void
    {
        $workbook = [
            [['Daerah', 'Jumlah'], ['Jakarta', 5], ['Bandung', 7]],
        ];

        $result = $this->extractor->parseWorkbook($workbook, ['Sebaran']);

        $this->assertSame('pie', $result[0]['chart_type']);
    }

    public function test_parse_workbook_skips_empty_and_non_numeric_rows(): void
    {
        $workbook = [
            [['Tahun', 'Jumlah'], ['', null], ['2023', 'bukan-angka'], ['2024', 50]],
        ];

        $result = $this->extractor->parseWorkbook($workbook, ['Mahasiswa']);

        $this->assertCount(1, $result);
        $this->assertSame([['label' => '2024', 'value' => 50.0]], $result[0]['items']);
    }

    public function test_parse_workbook_skips_sheets_without_items(): void
    {
        $workbook = [
            [['Tahun']],
            [['Tahun', 'Jumlah'], ['2023', 100]],
        ];

        $result = $this->extractor->parseWorkbook($workbook, ['Kosong', 'Isi']);

        $this->assertCount(1, $result);
        $this->assertSame('Isi', $result[0]['title']);
    }

    public function test_parse_workbook_falls_back_to_generated_sheet_name(): void
    {
        $workbook = [
            [['Tahun', 'Jumlah'], ['2023', 100]],
        ];

        $result = $this->extractor->parseWorkbook($workbook, []);

        $this->assertSame('Sheet 1', $result[0]['title']);
        $this->assertSame('Sheet 1', $result[0]['sheet_name']);
    }

    public function test_extract_reads_sheet_names_from_single_load_and_caps_sheets(): void
    {
        $spreadsheet = new Spreadsheet();

        for ($index = 0; $index < 21; $index++) {
            $sheet = $index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet($index);
            $sheet->setTitle('Data ' . ($index + 1));
            $sheet->fromArray([['Tahun', 'Jumlah'], ['2023', 100]]);
        }

        $path = tempnam(sys_get_temp_dir(), 'excel-cap') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        $result = $this->extractor->extract($path);

        unlink($path);

        $this->assertCount(20, $result);
        $this->assertSame('Data 1', $result[0]['title']);
        $this->assertSame('Data 20', $result[19]['title']);
    }

    public function test_extract_does_not_evaluate_formulas_on_import(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['Tahun', 'Jumlah'], ['=CMD|\'/C calc\'!A0', '=1+1'], ['2023', 100]]);

        $path = tempnam(sys_get_temp_dir(), 'excel-formula') . '.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        $result = $this->extractor->extract($path);

        unlink($path);

        $this->assertCount(1, $result);
        $this->assertSame([['label' => '2023', 'value' => 100.0]], $result[0]['items']);
    }
}
