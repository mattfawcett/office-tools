<?php
namespace Tests;

use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\Configure;
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
        Configuration::skipVerifyTls();
        Configuration::setProxy('localhost:8888');
        $inputPath = $this->inputFilePath('legacy.doc');
        $outputPath = $this->outputFilePath($converter, 'legacy-converted.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convert($inputPath, $outputPath, 'doc');
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'word/document.xml');
    }
}
