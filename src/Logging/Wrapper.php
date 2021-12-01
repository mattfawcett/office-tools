<?php
namespace WebmergeOfficeTools\Logging;

use RuntimeException;
use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\Exceptions\Exception;
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

        $this->withLogging(
            fn() => $this->baseImplementation->convertWordToPdf($filePath, $outputFilePath),
            "Converting word file to pdf",
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
