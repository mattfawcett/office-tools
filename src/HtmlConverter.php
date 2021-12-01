<?php
namespace WebmergeOfficeTools;

interface HtmlConverter
{
    /**
     * Convert a .html file to .docx
     **/
    public function convertHtmlToWord(string $filePath, string $outputFilePath): void;

    /**
     * Convert a .html file to .xlsx
     **/
    public function convertHtmlToExcel(string $filePath, string $outputFilePath): void;

    public function implementationName(): string;
}
