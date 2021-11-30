<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\LegacyFormatConverter as LegacyFormatConverterInterface;

class LegacyFormatConverter implements LegacyFormatConverterInterface
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void
    {
        $newFormat = LegacyFormatConverterInterface::LEGACY_FORMATS[$legacyFormat] ?? null;
        if (!$newFormat) {
            throw new ValidationException('Invalid legacy format ' . $legacyFormat);
        }


        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $conversionResponse = $this->client->post("/convert/$legacyFormat/to/$newFormat", [
            'File' => $fileId,
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}
