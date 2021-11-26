<?php
namespace Tests;

use WebmergeOfficeTools\Configuration;
use WebmergeOfficeTools\Implementations\ConvertApiDotCom;
use WebmergeOfficeTools\LegacyFormatConverter;
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
            [new ConvertApiDotCom\LegacyFormatConverter($this->convertApiClient())]
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

    protected function shouldRegenerate(string $path): bool
    {
        if (getenv('USE_API_CACHE') && file_exists($path)) {
            return false;
        }

        unlink($path);
        return true;
    }

    private function convertApiClient(): ConvertApiDotCom\HttpClient
    {
        return (new ConvertApiDotCom\HttpClient(new \GuzzleHttp\Client))->setSecret('POOUE3or0L5CvOAP');
    }
}
