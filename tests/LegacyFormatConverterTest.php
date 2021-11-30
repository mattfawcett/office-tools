<?php
namespace Tests;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\LegacyFormatConverter;

class LegacyFormatConverterTest extends TestCase
{
    /** @dataProvider legacyFormatConverterImplementations **/
    public function testConvertInvalidFormat(LegacyFormatConverter $converter)
    {
        $this->expectException(ValidationException::class);
        $converter->convertLegacyFormat($this->inputFilePath('text.txt'), $this->outputFilePath($converter, 'text.txt'), 'txt');
    }

    /** @dataProvider legacyFormatConverterImplementations **/
    public function testConvertDoc(LegacyFormatConverter $converter)
    {
        $inputPath = $this->inputFilePath('legacy.doc');
        $outputPath = $this->outputFilePath($converter, 'legacy-converted.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertLegacyFormat($inputPath, $outputPath, 'doc');
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'word/document.xml');
    }

    /** @dataProvider legacyFormatConverterImplementations **/
    public function testConvertXls(LegacyFormatConverter $converter)
    {
        if ($converter instanceof \WebmergeOfficeTools\Implementations\ConvertApiDotCom\Implementation) {
            $this->markTestSkipped('Convert API does not have xls to xlsx conversion yet');
        }

        $inputPath = $this->inputFilePath('legacy.xls');
        $outputPath = $this->outputFilePath($converter, 'legacy-converted.xlsx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertLegacyFormat($inputPath, $outputPath, 'xls');
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'xl/workbook.xml');
    }

    /** @dataProvider legacyFormatConverterImplementations **/
    public function testConvertPpt(LegacyFormatConverter $converter)
    {
        if ($converter instanceof \WebmergeOfficeTools\Implementations\ConvertApiDotCom\Implementation) {
            $this->markTestSkipped('Convert API does not have ppt to pptx conversion yet');
        }
        if ($converter instanceof \WebmergeOfficeTools\Implementations\LegacyWindows\Implementation) {
            $this->markTestSkipped('ppt to pptx conversion on legacy windows does not actually work');
        }

        $inputPath = $this->inputFilePath('legacy.ppt');
        $outputPath = $this->outputFilePath($converter, 'legacy-converted.pptx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertLegacyFormat($inputPath, $outputPath, 'ppt');
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'ppt/presentation.xml');
    }
}
