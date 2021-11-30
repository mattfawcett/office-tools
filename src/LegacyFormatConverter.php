<?php
namespace WebmergeOfficeTools;

interface LegacyFormatConverter
{
    public const LEGACY_FORMATS = [
        'doc' => 'docx',
        'xls' => 'xlsx',
        'ppt' => 'pptx'
    ];

    public function convert(string $filePath, string $outputFilePath, string $legacyFormat): void;
}
