<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use WebmergeOfficeTools\ExcelConverter;
use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\HtmlConverter;
use WebmergeOfficeTools\LegacyFormatConverter;
use WebmergeOfficeTools\PowerpointConverter;
use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordProtecter;

class Implementation implements WordConverter, WordProtecter, ExcelConverter, PowerpointConverter, HtmlConverter, LegacyFormatConverter
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

    public function convertPowerpointToPdf(string $filePath, string $outputFilePath): void
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

    public function convertHtmlToWord(string $filePath, string $outputFilePath): void
    {
        if (!preg_match('/\.(htm|html)$/', $filePath, $matches)) {
            throw new ValidationException('Html document must have html or htm extension');
        }

        $fileId = $this->client->uploadFile($filePath)['FileId'];

        $conversionResponse = $this->client->post("/convert/html/to/docx", [
            'File' => $fileId,
        ]);

        file_put_contents($outputFilePath, base64_decode($conversionResponse['Files'][0]['FileData']));
    }

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void
    {
        $newFormat = LegacyFormatConverter::LEGACY_FORMATS[$legacyFormat] ?? null;
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
