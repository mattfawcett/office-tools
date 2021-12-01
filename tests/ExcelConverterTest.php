<?php
namespace Tests;

use WebmergeOfficeTools\ExcelConverter;

class ExcelConverterTest extends TestCase
{
    /** @dataProvider excelConverterImplementations **/
    public function testBasicConvertToPdf(ExcelConverter $converter)
    {
        $inputPath = $this->inputFilePath('basic.xlsx');
        $outputPath = $this->outputFilePath($converter, 'basic.xlsx.pdf');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertExcelToPdf($inputPath, $outputPath);
        }

        $this->assertFileIsAPdf($outputPath);

        $png = $this->toPng($outputPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('basic.xlsx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }

    /** @dataProvider excelConverterImplementations **/
    public function testConvertToPdfWhenLegacyXls(ExcelConverter $converter)
    {
        $inputPath = $this->inputFilePath('legacy.xls');
        $outputPath = $this->outputFilePath($converter, 'legacy.xls.pdf');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertExcelToPdf($inputPath, $outputPath);
        }

        $this->assertFileIsAPdf($outputPath);

        $png = $this->toPng($outputPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('legacy.xls.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }

    /** @dataProvider excelConverterImplementations **/
    public function testWhenNeedsRecalcuation(ExcelConverter $converter)
    {
        $inputPath = $this->inputFilePath('needs-recalc.xlsx');
        $outputPath = $this->outputFilePath($converter, 'needs-recalc.xlsx.pdf');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertExcelToPdf($inputPath, $outputPath);
        }

        $this->assertFileIsAPdf($outputPath);

        $png = $this->toPng($outputPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('needs-recalc.xlsx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }
}
