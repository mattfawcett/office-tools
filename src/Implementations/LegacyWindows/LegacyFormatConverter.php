<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\LegacyFormatConverter as LegacyFormatConverterInterface;

class LegacyFormatConverter implements LegacyFormatConverterInterface
{
    private GeneralConverter $generalConverter;

    public function __construct(GeneralConverter $client)
    {
        $this->generalConverter = $client;
    }

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void
    {
        $newFormat = LegacyFormatConverterInterface::LEGACY_FORMATS[$legacyFormat] ?? null;
        if (!$newFormat) {
            throw new ValidationException('Invalid legacy format ' . $legacyFormat);
        }

        $this->generalConverter->convert($filePath, $outputFilePath);
    }
}
