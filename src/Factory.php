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
            return self::legacyWindowsImplementation();
        } else {
            return self::legacyConvertApiDotComImplementation();
        }
    }

    public static function excelConverter(string $system = null): ExcelConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::legacyWindowsImplementation();
        } else {
            return self::legacyConvertApiDotComImplementation();
        }
    }

    public static function powerpointConverter(string $system = null): PowerpointConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::legacyWindowsImplementation();
        } else {
            return self::legacyConvertApiDotComImplementation();
        }
    }

    public static function htmlConverter(string $system = null): HtmlConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::legacyWindowsImplementation();
        } else {
            return self::legacyConvertApiDotComImplementation();
        }
    }

    public static function wordProtecter(string $system = null): WordProtecter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::legacyWindowsImplementation();
        } else {
            return self::legacyConvertApiDotComImplementation();
        }
    }

    public static function legacyFormatConverter(string $system = null): LegacyFormatConverter
    {
        if ($system === self::SYSTEM_LEGACY_WINDOWS) {
            return self::legacyWindowsImplementation();
        } else {
            return self::legacyConvertApiDotComImplementation();
        }
    }

    private static function cacheOrBuild(string $className, $callable)
    {
        if (!isset(self::$cachedClasses[$className])) {
            self::$cachedClasses[$className] = $callable();
        }

        return self::$cachedClasses[$className];
    }

    private static function legacyWindowsImplementation(): LegacyWindows\Implementation
    {
        return self::cacheOrBuild(LegacyWindows\Implementation::class, function() {
            return new LegacyWindows\Implementation(new GuzzleHttp\Client);
        });
    }

    private static function legacyConvertApiDotComImplementation(): ConvertApiDotCom\Implementation
    {
        return self::cacheOrBuild(ConvertApiDotCom\Implementation::class, function() {
            $http = (new ConvertApiDotCom\HttpClient(new GuzzleHttp\Client))->setSecret('POOUE3or0L5CvOAP');
            return new ConvertApiDotCom\Implementation($http);
        });
    }
}
