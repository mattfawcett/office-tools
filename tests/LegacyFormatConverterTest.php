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
        $converter->convert($this->inputFilePath('text.txt'), $this->outputFilePath($converter, 'text.txt'), 'txt');
    }

    /** @dataProvider legacyFormatConverterImplementations **/
    public function testConvertDoc(LegacyFormatConverter $converter)
    {
        $inputPath = $this->inputFilePath('legacy.doc');
        $outputPath = $this->outputFilePath($converter, 'legacy-converted.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convert($inputPath, $outputPath, 'doc');
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'word/document.xml');
    }

    /** @dataProvider legacyFormatConverterImplementations **/
    public function testConvertXls(LegacyFormatConverter $converter)
    {
        $inputPath = $this->inputFilePath('legacy.xls');
        $outputPath = $this->outputFilePath($converter, 'legacy-converted.xlsx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convert($inputPath, $outputPath, 'xls');
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'word/document.xml');
    }
}
