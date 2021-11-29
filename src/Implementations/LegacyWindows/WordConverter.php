<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\WordConverter as WordConverterInterface;
use WebmergeOfficeTools\WordProtecter;

class WordConverter implements WordConverterInterface, WordProtecter
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

    public function passwordProtect(string $filePath, string $outupFilePath, string $password): void
    {
        $this->generalConverter->convert($filePath, $outupFilePath, ['password' => $password]);
    }
}
