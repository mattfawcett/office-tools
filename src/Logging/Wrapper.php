<?php

namespace WebmergeOfficeTools\Logging;

use RuntimeException;
use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\ExcelConverter;
use WebmergeOfficeTools\Exceptions\Exception;
use WebmergeOfficeTools\HtmlConverter;
use WebmergeOfficeTools\LegacyFormatConverter;
use WebmergeOfficeTools\PowerpointConverter;
use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordProtecter;

class Wrapper implements WordConverter, ExcelConverter, PowerpointConverter, HtmlConverter, LegacyFormatConverter, WordProtecter
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

        $this->withLogging(
            fn () => $this->baseImplementation->convertWordToPdf($filePath, $outputFilePath),
            "Converting word file to pdf",
            compact('filePath', 'outputFilePath')
        );
    }

    public function convertExcelToPdf(string $filePath, string $outputFilePath): void
    {
        $this->assertBaseImplementationIs(ExcelConverter::class);
        assert($this->baseImplementation instanceof ExcelConverter);

        $this->withLogging(
            fn () => $this->baseImplementation->convertExcelToPdf($filePath, $outputFilePath),
            "Converting excel file to pdf",
            compact('filePath', 'outputFilePath')
        );
    }

    public function convertPowerpointToPdf(string $filePath, string $outputFilePath): void
    {
        $this->assertBaseImplementationIs(PowerpointConverter::class);
        assert($this->baseImplementation instanceof PowerpointConverter);

        $this->withLogging(
            fn () => $this->baseImplementation->convertPowerpointToPdf($filePath, $outputFilePath),
            "Converting powerpoint file to pdf",
            compact('filePath', 'outputFilePath')
        );
    }

    public function convertHtmlToWord(string $filePath, string $outputFilePath): void
    {
        $this->assertBaseImplementationIs(HtmlConverter::class);
        assert($this->baseImplementation instanceof HtmlConverter);

        $this->withLogging(
            fn () => $this->baseImplementation->convertHtmlToWord($filePath, $outputFilePath),
            "Converting html file to word",
            compact('filePath', 'outputFilePath')
        );
    }

    public function convertLegacyFormat(string $filePath, string $outputFilePath, string $legacyFormat): void
    {
        $this->assertBaseImplementationIs(LegacyFormatConverter::class);
        assert($this->baseImplementation instanceof LegacyFormatConverter);

        $this->withLogging(
            fn () => $this->baseImplementation->convertLegacyFormat($filePath, $outputFilePath, $legacyFormat),
            "Converting legacy office format to newer xml based format",
            compact('filePath', 'outputFilePath', 'legacyFormat')
        );
    }

    public function passwordProtectWordFile(string $filePath, string $outputFilePath, string $password): void
    {
        $this->assertBaseImplementationIs(WordProtecter::class);
        assert($this->baseImplementation instanceof WordProtecter);

        $this->withLogging(
            fn () => $this->baseImplementation->passwordProtectWordFile($filePath, $outputFilePath, $password),
            "Password protecting word file",
            compact('filePath', 'outputFilePath')
        );
    }

    private function withLogging(callable $run, string $message, array $additionalData): void
    {
        $debugBacktrace = array_map(function ($trace) {
            return [
                'file' => $trace['file'] ?? null,
                'line' => $trace['line'] ?? null,
                'function' => $trace['function'] ?? null,
            ];
        }, array_slice(debug_backtrace(), 0, 5));


        $startTimeMicroSeconds = microtime(true);
        try {
            $run();
            $deltaTimeMilliseconds = intval(1000 * (microtime(true) - $startTimeMicroSeconds));
            Configuration::logger()->info("$message: success", array_merge($additionalData, [
                'deltaTimeMilliseconds' => $deltaTimeMilliseconds,
                'implementationName' => $this->baseImplementation->implementationName(),
                'debug_backtrace' => $debugBacktrace,
            ]));
        } catch (Exception $e) {
            $deltaTimeMilliseconds = intval(1000 * (microtime(true) - $startTimeMicroSeconds));
            Configuration::logger()->error("$message: failure", array_merge($additionalData, [
                'deltaTimeMilliseconds' => $deltaTimeMilliseconds,
                'implementationName' => $this->baseImplementation->implementationName(),
                'debug_backtrace' => $debugBacktrace,
            ]));

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
