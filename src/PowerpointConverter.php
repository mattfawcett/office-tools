<?php
namespace WebmergeOfficeTools;

interface PowerpointConverter
{
    /**
     * Convert a pptx file into a pdf.
     **/
    public function convertToPdf(string $filePath, string $outputFilePath): void;
}

