<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\PowerpointConverter;

class PowerpointTools implements PowerpointConverter
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertToPdf(string $filePath, string $outputFilePath): void
    {
        $fileId = $this->client->uploadFile($filePath)['FileId'];

        if (!preg_match('/\.pptx$/', $filePath, $matches)) {
            throw new ValidationException('Powerpoint document must have pptx extension');
        }

        $conversionResponse = $this->client->post("/convert/pptx/to/pdf", [
            'File' => $fileId,
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}
