<?php
namespace Tests;

use WebmergeOfficeTools\PowerpointConverter;

class PowerpointConverterTest extends TestCase
{
    /** @dataProvider powerpointConverterImplementations **/
    public function testBasicConvert(PowerpointConverter $converter)
    {
        $inputPath = $this->inputFilePath('basic.pptx');
        $outputPath = $this->outputFilePath($converter, 'basic.pptx.pdf');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertToPdf($inputPath, $outputPath);
        }

        $this->assertFileIsAPdf($outputPath);

        $png = $this->toPng($outputPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('basic.pptx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }
}

