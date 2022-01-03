<?php

namespace Tests;

use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\ExcelConverter;
use WebmergeOfficeTools\Factory;
use WebmergeOfficeTools\HtmlConverter;
use WebmergeOfficeTools\LegacyFormatConverter;
use WebmergeOfficeTools\PowerpointConverter;
use WebmergeOfficeTools\WordConverter;
use WebmergeOfficeTools\WordFieldsUpdater;
use WebmergeOfficeTools\WordProtecter;
use ZipArchive;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (getenv('PROXY_TO_CHARLES')) {
            Configuration::skipVerifyTls();
            Configuration::setProxy('localhost:8888');
        }

        \PHPUnit\Framework\TestCase::setUpBeforeClass();
    }

    /** @return LegacyFormatConverter[] **/
    public function legacyFormatConverterImplementations(): array
    {
        return [
            [Factory::legacyFormatConverter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::legacyFormatConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    /** @return WordConverter[] **/
    public function wordConverterImplementations(): array
    {
        return [
            [Factory::wordConverter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::wordConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    /** @return WordProtecter[] **/
    public function wordProtecterImplementations(): array
    {
        return [
            [Factory::wordProtecter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::wordProtecter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    /** @return ExcelConverter[] **/
    public function excelConverterImplementations(): array
    {
        return [
            [Factory::excelConverter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::excelConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    /** @return PowerpointConverter[] **/
    public function powerpointConverterImplementations(): array
    {
        return [
            [Factory::powerpointConverter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::powerpointConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    /** @return HtmlConverter[] **/
    public function htmlConverterImplementations(): array
    {
        return [
            [Factory::htmlConverter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::htmlConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    /** @return WordFieldsUpdater[] **/
    public function wordFieldUpdaterImplementations(): array
    {
        return [
            [Factory::htmlConverter(Factory::SYSTEM_LEGACY_WINDOWS)],
            [Factory::htmlConverter(Factory::SYSTEM_CONVERTAPI_DOT_COM)],
        ];
    }

    protected function inputFilePath(string $fileName): string
    {
        return dirname(__FILE__) . '/input_files/' . $fileName;
    }

    protected function outputFilePath($class, $fileName): string
    {
        $implementationName = $class->implementationName();

        $folder = dirname(__FILE__) . '/output_files/' . $implementationName;
        if (!is_dir($folder)) {
            mkdir($folder);
        }
        return $folder . '/' . $fileName;
    }

    protected function benchmarkFilePath(string $fileName): string
    {
        return dirname(__FILE__) . '/benchmark_files/' . $fileName;
    }

    protected function assertZip(string $path): ZipArchive
    {
        $this->assertFileExists($path);
        $zip = new ZipArchive;
        $zip->open($path);

        return $zip;
    }

    protected function assertZipHasFileNamed(ZipArchive $zip, string $fileName): ?int
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stats = $zip->statIndex($i);
            if ($stats['name'] === $fileName) {
                $this->addToAssertionCount(1);
                return $i;
            }
        }

        $this->fail("Zip did not have file named $fileName");
        return null;
    }

    protected function assertFileIsAPdf(string $path): void
    {
        $this->assertEquals('application/pdf', mime_content_type($path));
    }

    protected function assertImagesSimilar(string $fileA, string $fileB): void
    {
        exec("idiff -fail 0.004 -failpercent 5 $fileA $fileB", $output);

        if ($output[1] === 'PASS') {
            $this->addToAssertionCount(1);
        } else {
            assert(preg_match("/ Mean error = /", $output[1]));
            $meanError = (float) str_replace(" Mean error = ", '', $output[1]);
            $stats = [];
            $stats['meanError'] = $meanError;

            assert(preg_match("/ pixels /", $output[6]));
            $matches = [];
            preg_match("/([\d\.]+)%/", $output[6], $matches);
            $percentFailed = (float) $matches[1];
            $stats['percentPixelsFail'] = $percentFailed;
            $stats['status'] = $output[7];

            $text = "Images do not match: a:$fileA, b:$fileB - " . json_encode($stats, JSON_PRETTY_PRINT);
            if ($stats['status'] === 'WARNING') {
                $this->addWarning($text);
            } else {
                $this->fail($text);
            }
        }
    }

    protected function toPng(string $pdfPath): string
    {
        $hash = sha1_file($pdfPath);
        $pngPath = dirname(__FILE__) . '/output_files/pngs/' . $hash . '.png';
        if (!file_exists($pngPath)) {
            $command = "convert -quality 100 -density 400 -resize 25% -flatten {$pdfPath}[0] {$pngPath}";
            exec($command);
        }

        return $pngPath;
    }

    protected function shouldRegenerate(string $path): bool
    {
        if (getenv('USE_API_CACHE') && file_exists($path)) {
            return false;
        }

        if (file_exists($path)) {
            unlink($path);
        }

        return true;
    }
}
