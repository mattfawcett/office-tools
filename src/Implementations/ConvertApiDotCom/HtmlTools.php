<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\HtmlConverter;

class HtmlTools implements HtmlConverter
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertToWord(string $filePath, string $outputFilePath): void
    {
        if (!preg_match('/\.(htm|html)$/', $filePath, $matches)) {
            throw new ValidationException('Html document must have html or htm extension');
        }

        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $inputExtension = $matches[1];

        $conversionResponse = $this->client->post("/convert/html/to/docx", [
            'File' => $fileId,
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}
