<?php
namespace Tests;

use WebmergeOfficeTools\WordProtecter;

class WordProtecterTest extends TestCase
{
    /** @dataProvider wordProtecterImplementations **/
    public function testBasicConvert(WordProtecter $converter)
    {
        $inputPath = $this->inputFilePath('basic.docx');
        $outputPath = $this->outputFilePath($converter, 'basic.encrypted.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->passwordProtect($inputPath, $outputPath, 'letmein');
        }

        $this->assertEquals('application/encrypted', mime_content_type($outputPath));
    }
}
