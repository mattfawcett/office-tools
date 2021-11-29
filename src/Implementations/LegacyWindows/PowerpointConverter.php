<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use WebmergeOfficeTools\PowerpointConverter as PowerpointConverterInterface;

class PowerpointConverter implements PowerpointConverterInterface
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

