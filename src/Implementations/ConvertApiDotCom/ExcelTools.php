<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\ExcelConverter as ExcelConverterInterface;

class ExcelTools implements ExcelConverterInterface
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertToPdf(string $filePath, string $outputFilePath): void
    {
        if (!preg_match('/\.(xlsx|xls)$/', $filePath, $matches)) {
            throw new ValidationException('Excel document must have xlsx extension');
        }

        $fileId = $this->client->uploadFile($filePath)['FileId'];
        $inputExtension = $matches[1];

        $conversionResponse = $this->client->post("/convert/$inputExtension/to/pdf", [
            'File' => $fileId,
            'PageSize' => 'letter',
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}
