<?php
namespace WebmergeOfficeTools;

interface HtmlConverter
{
    /**
     * Convert either a .html file to .docx
     **/
    public function convertToWord(string $filePath, string $outputFilePath): void;
}
