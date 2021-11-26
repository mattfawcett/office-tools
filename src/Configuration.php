<?php
namespace WebmergeOfficeTools;

class Configuration
{
    public static ?string $proxy = null;

    public static bool $verifyTls = true;

    public static function setProxy(?string $proxy): void
    {
        self::$proxy = $proxy;
    }

    public static function skipVerifyTls(): void
    {
        self::$verifyTls = false;
    }
}
