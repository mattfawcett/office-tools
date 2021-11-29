<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
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
        if (!preg_match('/\.(doc|docx)$/', $filePath, $matches)) {
            throw new ValidationException('Word document must have doc or docx extension');
        }

        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $inputExtension = $matches[1];

        $conversionResponse = $this->client->post("/convert/$inputExtension/to/pdf", [
            'File' => $fileId,
        ]);

        file_put_contents($outupFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}

