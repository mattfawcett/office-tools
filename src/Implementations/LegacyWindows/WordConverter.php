<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\WordConverter as WordConverterInterface;

class WordConverter implements WordConverterInterface
{
    private GeneralConverter $generalConverter;

    public function __construct(GeneralConverter $client)
    {
        $this->generalConverter = $client;
    }

    public function convertToPdf(string $filePath, string $outupFilePath): void
    {
        $this->generalConverter->convert($filePath, $outupFilePath);
    }
}
