<?php
namespace WebmergeOfficeTools\Logging\Wrappers;

use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\WordConverter;

class WordConverterWrapper implements WordConverter
{
    private WordConverter $baseImplementation;

    private function __construct(WordConverter $baseImplementation)
    {
        $this->baseImplementation = $baseImplementation;
    }

    public static function wrap(WordConverter $wordConverter)
    {
        return new self($wordConverter);
    }

    public function convertToPdf(string $filePath, string $outputFilePath): void
    {
        try {
            $this->baseImplementation->convertToPdf($filePath, $outputFilePath);
            Configuration::logger()->info('Converted word file to pdf', [
                'inputFilePath' => $filePath,
                'outputFilePath' => $outputFilePath,
            ]);
        } catch (\Exception $e) {
            Configuration::logger()->error('Error converted word file to pdf', [
                'inputFilePath' => $filePath,
                'outputFilePath' => $outputFilePath,
            ]);
            throw $e;
        }
    }
}
