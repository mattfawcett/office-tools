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

        $png = $this->toPng($outputPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('basic.docx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);
    }

    /** @dataProvider wordConverterImplementations **/
    public function testShouldHaveCorrectTableOfContentsEvenIfNotUpdatedInWordDoc(WordConverter $converter)
    {
        $inputPath = $this->inputFilePath('toc-not-updated.docx');
        $outputPath = $this->outputFilePath($converter, 'toc-not-updated.docx.pdf');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->convertToPdf($inputPath, $outputPath);
        }

        $this->assertFileIsAPdf($outputPath);

        $png = $this->toPng($outputPath);

        $benchmark = $this->toPng($this->benchmarkFilePath('toc-not-updated.docx.pdf'));

        $this->assertImagesSimilar($benchmark, $png);

        echo $this->ocr($png);
    }


}
