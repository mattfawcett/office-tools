<?php
namespace Tests;

use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\ExcelConverter;
use WebmergeOfficeTools\Implementations\ConvertApiDotCom;
use WebmergeOfficeTools\Implementations\LegacyWindows;
use WebmergeOfficeTools\LegacyFormatConverter;
use WebmergeOfficeTools\WordConverter;
use ZipArchive;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        if(getenv('PROXY_TO_CHARLES')) {
            Configuration::skipVerifyTls();
            Configuration::setProxy('localhost:8888');
        }

        \PHPUnit\Framework\TestCase::setUpBeforeClass();
    }

    /** @return LegacyFormatConverter[] **/
    public function legacyFormatConverterImplementations(): array
    {
        return [
            [new ConvertApiDotCom\LegacyFormatConverter($this->convertApiClient())],
            [new LegacyWindows\LegacyFormatConverter($this->legacyWindowsGeneralConverter())],
        ];
    }

    /** @return WordConverter[] **/
    public function wordConverterImplementations(): array
    {
        return [
            [new ConvertApiDotCom\WordConverter($this->convertApiClient())],
            [new LegacyWindows\WordConverter($this->legacyWindowsGeneralConverter())],
        ];
    }

    /** @return ExcelConverter[] **/
    public function excelConverterImplementations(): array
    {
        return [
            [new ConvertApiDotCom\ExcelConverter($this->convertApiClient())],
            [new LegacyWindows\ExcelConverter($this->legacyWindowsGeneralConverter())],
        ];
    }

    protected function inputFilePath(string $fileName): string
    {
        return dirname(__FILE__) . '/input_files/' . $fileName;
    }

    protected function outputFilePath($class, $fileName): string
    {
        $className = get_class($class);
        if (!preg_match('/WebmergeOfficeTools\\\\Implementations/', $className)) {
            $this->fail('Invalid class ' . $className);
        }

        $implementationName = explode('\\', $className)[2];

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
        for ($i=0; $i < $zip->numFiles; $i++) {
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
        exec("idiff -fail 0.004 -failpercent 20 $fileA $fileB", $output);

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

    private function convertApiClient(): ConvertApiDotCom\HttpClient
    {
        return (new ConvertApiDotCom\HttpClient(new \GuzzleHttp\Client))->setSecret('POOUE3or0L5CvOAP');
    }

    private function legacyWindowsGeneralConverter(): LegacyWindows\GeneralConverter
    {
        return (new LegacyWindows\GeneralConverter(new \GuzzleHttp\Client));
    }
}
