<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordProtecter;

class WordTools implements WordConverter, WordProtecter
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertWordToPdf(string $filePath, string $outputFilePath): void
    {
        if (!preg_match('/\.(doc|docx)$/', $filePath, $matches)) {
            throw new ValidationException('Word document must have doc or docx extension');
        }

        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $inputExtension = $matches[1];

        $conversionResponse = $this->client->post("/convert/$inputExtension/to/pdf", [
            'File' => $fileId,
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }

    public function passwordProtectWordFile(string $filePath, string $outputFilePath, string $password): void
    {
        if (!preg_match('/\.docx$/', $filePath, $matches)) {
            throw new ValidationException('Word document must have docx extension');
        }

        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $conversionResponse = $this->client->post("/convert/docx/to/encrypt", [
            'File' => $fileId,
            'EncryptPassword' => $password,
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}

