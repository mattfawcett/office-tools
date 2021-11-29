<?php
namespace WebmergeOfficeTools\Implementations\LegacyWindows;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\Exceptions;

class GeneralConverter
{
    private const ENDPOINT = 'https://windows.webmerge.me/convert';

    private GuzzleHttp\ClientInterface $guzzle;

    public function __construct(GuzzleHttp\ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function convert(string $filePath, string $outputFilePath): void
    {
        $fileContents = file_get_contents($filePath);

        try {
            $response = $this->guzzle->request('POST', self::ENDPOINT, [
                'form_params' => [
                    'file_format' => $this->fileExtension($filePath),
                    'file_contents' => base64_encode($fileContents),
                    'output_format' => $this->fileExtension($outputFilePath),
                ],
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

