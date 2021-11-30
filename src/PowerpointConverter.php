<?php
namespace WebmergeOfficeTools;

interface PowerpointConverter
{
    /**
     * Convert a pptx file into a pdf.
     **/
    public function convertPowerpointToPdf(string $filePath, string $outputFilePath): void;
}

