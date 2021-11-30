<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordProtecter;

class WordTools implements WordConverter, WordProtecter
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
}
