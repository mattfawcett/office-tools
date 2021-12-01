<?php
namespace Tests;

use WebmergeOfficeTools\WordFieldsUpdater;

class WordFieldsUpdaterTest extends TestCase
{
    /** @dataProvider wordFieldUpdaterImplementations **/
    public function testUpdateFieldsInWordDocument(WordFieldsUpdater $converter)
    {
        $inputPath = $this->inputFilePath('needs-fields-updating.docx');
        $outputPath = $this->outputFilePath($converter, 'needs-fields-updating-now-updated.docx');

        if ($this->shouldRegenerate($outputPath)) {
            $converter->updateFieldsInWordDocument($inputPath, $outputPath);
        }

        $zip = $this->assertZip($outputPath);
        $this->assertZipHasFileNamed($zip, 'word/document.xml');
    }
}
