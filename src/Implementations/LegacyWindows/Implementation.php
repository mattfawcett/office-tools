<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\ExcelConverter;
use WebmergeOfficeTools\Exceptions;
use WebmergeOfficeTools\Exceptions\ValidationException;
use WebmergeOfficeTools\HtmlConverter;
use WebmergeOfficeTools\LegacyFormatConverter;
use WebmergeOfficeTools\PowerpointConverter;
use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordProtecter;

class Implementation implements WordConverter, WordProtecter, ExcelConverter, PowerpointConverter, HtmlConverter, LegacyFormatConverter
{
    private const ENDPOINT = 'https://windows.webmerge.me/convert';

    private GuzzleHttp\ClientInterface $guzzle;

    public function __construct(GuzzleHttp\ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function implementationName(): string
    {
        return 'legacy_windows';
    }

    public function convertWordToPdf(string $filePath, string $outputFilePath): void
    {
        $this->convert($filePath, $outputFilePath);
    }

    public function passwordProtectWordFile(string $filePath, string $outputFilePath, string $password): void
    {
        $this->convert($filePath, $outputFilePath, ['password' => $password]);
    }

    public function convertToPdf(string $filePath, string $outputFilePath): void
    {
        $this->convert($filePath, $outputFilePath);
    }

    public function convertPowerpointToPdf(string $filePath, string $outputFilePath): void
    {
        $this->convert($filePath, $outputFilePath);
    }

    public function convertHtmlToWord(string $filePath, string $outputFilePath): void
    {
        $this->convert($filePath, $outputFilePath);
    }

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void
    {
        $newFormat = LegacyFormatConverter::LEGACY_FORMATS[$legacyFormat] ?? null;
        if (!$newFormat) {
            throw new ValidationException('Invalid legacy format ' . $legacyFormat);
        }

        $this->generalConverter->convert($filePath, $outputFilePath);
    }

    private function convert(string $filePath, string $outputFilePath, array $qsParams = []): void
    {
        $fileContents = file_get_contents($filePath);

        try {
            $response = $this->guzzle->request('POST', self::ENDPOINT, [
                'form_params' => [
                    'file_format' => $this->fileExtension($filePath),
                    'file_contents' => base64_encode($fileContents),
                    'output_format' => $this->fileExtension($outputFilePath),
                ],
                'query' => $qsParams,
                'proxy' => Configuration::$proxy,
                'verify' => Configuration::$verifyTls,
            ]);
        } catch(GuzzleException $e) {
            throw new Exceptions\ApiException('Error when uploading to legacy windows API', 0, $e);
        }

        file_put_contents($outputFilePath, $response->getBody());
    }

    private function fileExtension(string $path): string
    {
        $pathSegments = explode('/', $path);
        $fileName = end($pathSegments);

        $fileParts = explode('.', $fileName);
        $extension = end($fileParts);

        return $extension;
    }
}
