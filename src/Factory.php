<?php
namespace WebmergeOfficeTools;

use GuzzleHttp;
use WebmergeOfficeTools\Implementations\ConvertApiDotCom;
use WebmergeOfficeTools\Implementations\LegacyWindows;

class Factory
{
    public const SYSTEM_LEGACY_WINDOWS     = 'legacy_windows';
    public const SYSTEM_CONVERTAPI_DOT_COM = 'convert_api_dot_com';

    private static $cachedClasses = [];

    public static function wordConverter(string $system = null): WordConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::cacheOrBuild(LegacyWindows\WordTools::class, function() {
                return new LegacyWindows\WordTools(self::legacyWindowsGeneralConverter());
            });
        } else {
            return self::cacheOrBuild(ConvertApiDotCom\WordTools::class, function() {
                return new ConvertApiDotCom\WordTools(self::convertApiClient());
            });
        }
    }

    public static function excelConverter(string $system = null): ExcelConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::cacheOrBuild(LegacyWindows\ExcelTools::class, function() {
                return new LegacyWindows\ExcelTools(self::legacyWindowsGeneralConverter());
            });
        } else {
            return self::cacheOrBuild(ConvertApiDotCom\ExcelTools::class, function() {
                return new ConvertApiDotCom\ExcelTools(self::convertApiClient());
            });
        }
    }

    public static function powerpointConverter(string $system = null): PowerpointConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::cacheOrBuild(LegacyWindows\PowerpointTools::class, function() {
                return new LegacyWindows\PowerpointTools(self::legacyWindowsGeneralConverter());
            });
        } else {
            return self::cacheOrBuild(ConvertApiDotCom\PowerpointTools::class, function() {
                return new ConvertApiDotCom\PowerpointTools(self::convertApiClient());
            });
        }
    }

    public static function htmlConverter(string $system = null): HtmlConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::cacheOrBuild(LegacyWindows\HtmlTools::class, function() {
                return new LegacyWindows\HtmlTools(self::legacyWindowsGeneralConverter());
            });
        } else {
            return self::cacheOrBuild(ConvertApiDotCom\HtmlTools::class, function() {
                return new ConvertApiDotCom\HtmlTools(self::convertApiClient());
            });
        }
    }

    public static function wordProtecter(string $system = null): WordProtecter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::cacheOrBuild(LegacyWindows\Word::class, function() {
                return new LegacyWindows\WordTools(self::legacyWindowsGeneralConverter());
            });
        } else {
            return self::cacheOrBuild(ConvertApiDotCom\WordTools::class, function() {
                return new ConvertApiDotCom\WordTools(self::convertApiClient());
            });
        }
    }

    public static function legacyFormatConverter(string $system = null): LegacyFormatConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::cacheOrBuild(LegacyWindows\LegacyFormatConverter::class, function() {
                return new LegacyWindows\LegacyFormatConverter(self::legacyWindowsGeneralConverter());
            });
        } else {
            return self::cacheOrBuild(ConvertApiDotCom\LegacyFormatConverter::class, function() {
                return new ConvertApiDotCom\LegacyFormatConverter(self::convertApiClient());
            });
        }
    }

    private static function convertApiClient(): ConvertApiDotCom\HttpClient
    {
        return self::cacheOrBuild(ConvertApiDotCom\HttpClient::class, function() {
            return (new ConvertApiDotCom\HttpClient(new GuzzleHttp\Client))->setSecret('POOUE3or0L5CvOAP');
        });
    }

    private static function legacyWindowsGeneralConverter(): LegacyWindows\GeneralConverter
    {
        return self::cacheOrBuild(LegacyWindows\GeneralConverter::class, function() {
            return (new LegacyWindows\GeneralConverter(new GuzzleHttp\Client));
        });
    }

    private static function cacheOrBuild(string $className, $callable)
    {
        if (!isset(self::$cachedClasses[$className])) {
            self::$cachedClasses[$className] = $callable();
        }

        return self::$cachedClasses[$className];
    }
}
