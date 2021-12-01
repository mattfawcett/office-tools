<?php
namespace Tests;

use WebmergeOfficeTools\Factory;
use WebmergeOfficeTools\HtmlConverter;

class HtmlConverterTest extends TestCase
{
    /** @dataProvider htmlConverterImplementations **/
    public function testConvertHtmlToWordWhenHeaderAndFooterInSingleTable(HtmlConverter $converter)
    {
        $this->markTestSkipped('Newer office does not seem to like both header and footer in single table');
        $inputPath = $this->inputFilePath('header-footer-in-single-table.html');
        $outputPath = $this->outputFilePath($converter, 'header-footer-in-single-table.html.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertHtmlToWord($inputPath, $outputPath);
        }

        $zip = $this->assertZip($outputPath);
        $fileIndex = $this->assertZipHasFileNamed($zip, 'word/document.xml');

        $document = $zip->getFromIndex($fileIndex);
        $this->assertStringContainsString('<w:headerReference', $document);
    }

    /** @dataProvider htmlConverterImplementations **/
    public function testConvertHtmlToWordWhenHeaderAndFooterInSubTables(HtmlConverter $converter)
    {
        $inputPath = $this->inputFilePath('header-footer-in-sub-tables.html');
        $outputPath = $this->outputFilePath($converter, 'header-footer-in-sub-tables.html.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertHtmlToWord($inputPath, $outputPath);
        }

        $zip = $this->assertZip($outputPath);
        $fileIndex = $this->assertZipHasFileNamed($zip, 'word/document.xml');

        $document = $zip->getFromIndex($fileIndex);
        $this->assertStringContainsString('<w:headerReference', $document);

        $docxHash = sha1_file($outputPath);
        $pdfPath = $this->outputFilePath($converter, "header-footer-in-sub-tables.html.docx.$docxHash.pdf");
        if (!file_exists($pdfPath)) {
            $converter = Factory::wordConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM);
            $converter->convertWordToPdf($outputPath, $pdfPath);
        }

        $png = $this->toPng($pdfPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('header-footer-in-sub-tables.html.docx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }

    /** @dataProvider htmlConverterImplementations **/
    public function testConvertHtmlToExcel(HtmlConverter $converter)
    {
        $inputPath = $this->inputFilePath('html-for-excel-conversion.html');
        $outputPath = $this->outputFilePath($converter, 'html-for-excel-conversion.html.xlsx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertHtmlToExcel($inputPath, $outputPath);
        }

        $zip = $this->assertZip($outputPath);
        $fileIndex = $this->assertZipHasFileNamed($zip, 'xl/workbook.xml');

        $xlsxHash = sha1_file($outputPath);
        $pdfPath = $this->outputFilePath($converter, "html-for-excel-conversion.html.xlsx.$xlsxHash.pdf");
        if (!file_exists($pdfPath)) {
            $converter = Factory::excelConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM);
            $converter->convertExcelToPdf($outputPath, $pdfPath);
        }

        $png = $this->toPng($pdfPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('html-for-excel-conversion.html.xlsx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }
}
