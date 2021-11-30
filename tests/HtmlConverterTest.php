<?php
namespace Tests;

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
    }
}
