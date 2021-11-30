<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\ExcelConverter;
use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\HtmlConverter;
use WebmergeOfficeTools\LegacyFormatConverter;
use WebmergeOfficeTools\PowerpointConverter;
use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordProtecter;

class Implementation implements WordConverter, WordProtecter, ExcelConverter, PowerpointConverter, HtmlConverter, LegacyFormatConverter
{
    private GeneralConverter $generalConverter;

    public function __construct(GeneralConverter $client)
    {
        $this->generalConverter = $client;
    }

    public function convertWordToPdf(string $filePath, string $outputFilePath): void
    {
        $this->generalConverter->convert($filePath, $outputFilePath);
    }

    public function passwordProtectWordFile(string $filePath, string $outputFilePath, string $password): void
    {
        $this->generalConverter->convert($filePath, $outputFilePath, ['password' => $password]);
    }

    public function convertToPdf(string $filePath, string $outputFilePath): void
    {
        $this->generalConverter->convert($filePath, $outputFilePath);
    }

    public function convertPowerpointToPdf(string $filePath, string $outputFilePath): void
    {
        $this->generalConverter->convert($filePath, $outputFilePath);
    }

    public function convertHtmlToWord(string $filePath, string $outputFilePath): void
    {
        $this->generalConverter->convert($filePath, $outputFilePath);
    }

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void
    {
        $newFormat = LegacyFormatConverter::LEGACY_FORMATS[$legacyFormat] ?? null;
        if (!$newFormat) {
            throw new ValidationException('Invalid legacy format ' . $legacyFormat);
        }

        $this->generalConverter->convert($filePath, $outputFilePath);
    }
}
