<?php
namespace WebmergeOfficeTools;

use WebmergeOfficeTools\Logging\LoggingInterface;
use WebmergeOfficeTools\Logging\StdErrLogger;

class Configuration
{
    public static ?string $proxy = null;

    public static bool $verifyTls = true;

    private static ?LoggingInterface $logger;

    public static function setProxy(?string $proxy): void
    {
        self::$proxy = $proxy;
    }

    public static function skipVerifyTls(): void
    {
        self::$verifyTls = false;
    }

    public static function setLogger(LoggingInterface $logger)
    {
        self::$logger = $logger;
    }

    public static function logger(): LoggingInterface
    {
        return self::$logger ?? new StdErrLogger;
    }
}
