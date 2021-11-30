<?php
namespace WebmergeOfficeTools\Logging;

interface LoggingInterface
{
    public function info(string $message, array $data): void;
    public function warning(string $message, array $data): void;
    public function error(string $message, array $data): void;
}
