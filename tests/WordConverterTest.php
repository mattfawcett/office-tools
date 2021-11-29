<?php
namespace Tests;

use WebmergeOfficeTools\WordConverter;

class WordConverterTest extends TestCase
{
    /** @dataProvider wordConverterImplementations **/
    public function testBasicConvert(WordConverter $converter)
    {
        $inputPath = $this->inputFilePath('basic.docx');
        $outputPath = $this->outputFilePath($converter, 'basic.docx.pdf');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertToPdf($inputPath, $outputPath);
        }

        $this->assertFileIsAPdf($outputPath);
    }
}
