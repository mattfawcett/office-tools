<?php
namespace Tests;

use SimpleXMLElement;
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

        $this->assertToc($inputPath, [
            'Section 1', '1',
            'Section 2', '1',
            'Section 3', '1',
        ]);

        $this->assertToc($outputPath, [
            'Section 1', '1',
            'Section 2', '2',
            'Section 3', '2',
        ]);
    }

    private function assertToc(string $pathToDocx, array $data)
    {
        $zip = $this->assertZip($pathToDocx);
        $index = $this->assertZipHasFileNamed($zip, 'word/document.xml');

        $document = $zip->getFromIndex($index);
        $xml = new SimpleXMLElement($document);
        $contents = array_map(function($line) {
            return (string) $line;
        }, $xml->xpath('//w:sdtContent//w:hyperlink//w:t'));

        $this->assertEquals($data, $contents);
    }
}
