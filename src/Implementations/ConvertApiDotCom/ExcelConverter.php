<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\ExcelConverter as ExcelConverterInterface;

class ExcelConverter implements ExcelConverterInterface
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function convertToPdf(string $filePath, string $outupFilePath): void
    {
        $fileId = $this->client->uploadFile($filePath)['FileId'];

        if (!preg_match('/\.xlsx$/', $filePath, $matches)) {
            throw new ValidationException('Excel document must have xlsx extension');
        }

        $conversionResponse = $this->client->post("/convert/xlsx/to/pdf", [
            'File' => $fileId,
            'PageSize' => 'letter',
        ]);

        file_put_contents($outupFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }
}
