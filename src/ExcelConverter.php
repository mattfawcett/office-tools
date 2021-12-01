<?php
namespace WebmergeOfficeTools;

interface ExcelConverter
{
    /**
     * Convert either a .xlsx or .xls file into a pdf.
     *
     * Should recaululate formulas where necessary
     **/
    public function convertExcelToPdf(string $filePath, string $outputFilePath): void;

    public function implementationName(): string;
}
