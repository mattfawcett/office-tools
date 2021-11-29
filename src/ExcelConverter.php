<?php
namespace WebmergeOfficeTools;

interface ExcelConverter
{
    /**
     * Convert either a .xlsx or .xls file into a pdf.
     *
     * Should recaululate formulas where necessary
     **/
    public function convertToPdf(string $filePath, string $outupFilePath): void;
}

