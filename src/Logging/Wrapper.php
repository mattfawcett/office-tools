<?php
namespace WebmergeOfficeTools\Logging;

use RuntimeException;
use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\WordConverter;

class Wrapper implements WordConverter
{
    private $baseImplementation;

    private function __construct($baseImplementation)
    {
        $this->baseImplementation = $baseImplementation;
    }

    public static function wrap($wordConverter)
    {
        return new self($wordConverter);
    }

    public function implementationName(): string
    {
        return $this->baseImplementation->implementationName();
    }

    public function convertWordToPdf(string $filePath, string $outputFilePath): void
    {
        $this->assertBaseImplementationIs(WordConverter::class);
        assert($this->baseImplementation instanceof WordConverter);

        try {
            $this->baseImplementation->convertWordToPdf($filePath, $outputFilePath);
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

    private function assertBaseImplementationIs($interface)
    {
        if (!($this->baseImplementation instanceof $interface)) {
            throw new RuntimeException('Log wrapper expected base implementation of ' . $interface);
        }
    }
}
