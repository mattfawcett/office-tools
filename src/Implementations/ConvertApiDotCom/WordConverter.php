<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\WordConverter as WordConverterInterface;

class WordConverter implements WordConverterInterface
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertToPdf(string $filePath, string $outupFilePath): void
    {
        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $conversionResponse = $this->client->post("/convert/docx/to/pdf", [
            'File' => $fileId,
        ]);

        file_put_contents($outupFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}

