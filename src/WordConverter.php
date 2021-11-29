<?php
namespace WebmergeOfficeTools;

interface WordConverter
{
    public function convertToPdf(string $filePath, string $outupFilePath): void;
}
