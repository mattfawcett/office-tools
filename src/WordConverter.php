<?php
namespace WebmergeOfficeTools;

interface WordConverter
{
    /**
     * Convert either a .doc or .docx file into a pdf.
     *
     * docm files are not currently supported (UI is hidden to use pdf output for docm files)
     **/
    public function convertWordToPdf(string $filePath, string $outputFilePath): void;
}
