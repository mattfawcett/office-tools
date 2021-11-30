<?php
namespace Tests;

use WebmergeOfficeTools\Factory;
use WebmergeOfficeTools\HtmlConverter;

class HtmlConverterTest extends TestCase
{
    /** @dataProvider htmlConverterImplementations **/
    public function testConvertWhenHeaderAndFooterInSingleTable(HtmlConverter $converter)
    {
        $this->markTestSkipped('Newer office does not seem to like both header and footer in single table');
        $inputPath = $this->inputFilePath('header-footer-in-single-table.html');
        $outputPath = $this->outputFilePath($converter, 'header-footer-in-single-table.html.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertToWord($inputPath, $outputPath);
        }

        $zip = $this->assertZip($outputPath);
        $fileIndex = $this->assertZipHasFileNamed($zip, 'word/document.xml');

        $document = $zip->getFromIndex($fileIndex);
        $this->assertStringContainsString('<w:headerReference', $document);
    }

    /** @dataProvider htmlConverterImplementations **/
    public function testConvertWhenHeaderAndFooterInSubTables(HtmlConverter $converter)
    {
        $inputPath = $this->inputFilePath('header-footer-in-sub-tables.html');
        $outputPath = $this->outputFilePath($converter, 'header-footer-in-sub-tables.html.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertToWord($inputPath, $outputPath);
        }

        $zip = $this->assertZip($outputPath);
        $fileIndex = $this->assertZipHasFileNamed($zip, 'word/document.xml');

        $document = $zip->getFromIndex($fileIndex);
        $this->assertStringContainsString('<w:headerReference', $document);

        $docxHash = sha1_file($outputPath);
        $pdfPath = $this->outputFilePath($converter, "header-footer-in-sub-tables.html.docx.$docxHash.pdf");
        if (!file_exists($pdfPath)) {
            $this->convertToPdf($outputPath, $pdfPath);
        }

        $png = $this->toPng($pdfPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('header-footer-in-sub-tables.html.docx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }

    private function convertToPdf(string $docxPath, string $pdfPath): void
    {
        $converter = Factory::wordConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM);

        $converter->convertToPdf($docxPath, $pdfPath);
    }
}
