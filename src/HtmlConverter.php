<?php
namespace WebmergeOfficeTools;

interface HtmlConverter
{
    /**
     * Convert either a .html file to .docx
     **/
    public function convertHtmlToWord(string $filePath, string $outputFilePath): void;

    public function implementationName(): string;
}
