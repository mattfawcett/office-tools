<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\ExcelConverter;

class ExcelTools implements ExcelConverter
{
    private GeneralConverter $generalConverter;

    public function __construct(GeneralConverter $client)
    {
        $this->generalConverter = $client;
    }

    public function convertToPdf(string $filePath, string $outputFilePath): void
    {
        $this->generalConverter->convert($filePath, $outputFilePath);
    }
}

