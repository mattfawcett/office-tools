<?php
namespace WebmergeOfficeTools\Implementations\ConvertApiDotCom;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\Exceptions;

class HttpClient
{
    private const ENDPOINT = 'https://v2.convertapi.com';

    private GuzzleHttp\ClientInterface $guzzle;
    private ?string $secret = null;

    public function __construct(GuzzleHttp\ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function uploadFile(string $filePath): array
    {
        $body = GuzzleHttp\Psr7\Utils::tryFopen($filePath, 'r');
        $pathSegments = explode('/', $filePath);
        $fileName = end($pathSegments);

        try {
            $response = $this->guzzle->request('POST', $this->fullUrlWithSecret('/upload'), [
                'body' => $body,
                'proxy' => Configuration::$proxy,
                'verify' => Configuration::$verifyTls,
                'headers' => [
                    'Content-Disposition' => sprintf('inline; filename="%s"', urlencode($fileName)),
                ],
            ]);
        } catch(GuzzleException $e) {
            throw new Exceptions\ApiException('Error when uploading to convertapi.com API', 0, $e);
        }

        return json_decode($response->getBody(), true);
    }

    public function post(string $path, array $postData = []): array
    {
        try {
            $response = $this->guzzle->request('POST', $this->fullUrlWithSecret($path), [
                'form_params' => $postData,
                'proxy' => Configuration::$proxy,
                'verify' => Configuration::$verifyTls,
            ]);
        } catch(GuzzleException $e) {
            throw new Exceptions\ApiException('Error when posting to CloudConvert API', 0, $e);
        }

        return json_decode($response->getBody(), true);
    }

    private function fullUrlWithSecret(string $path): string
    {
        if (!$this->secret) {
            throw new Exceptions\ConfigurationException('No secret configured for convertapi.com API');
        }

        return self::ENDPOINT . $path . '?secret=' . $this->secret;
    }
}
