<?php
namespace WebmergeOfficeTools;

interface LegacyFormatConverter
{
    public const LEGACY_FORMATS = [
        'doc' => 'docx',
        'xls' => 'xlsx',
        'ppt' => 'pptx'
    ];

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void;
}
