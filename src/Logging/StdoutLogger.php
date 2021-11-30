<?php
namespace WebmergeOfficeTools\Logging;

class StdoutLogger implements LoggingInterface
{
    public function info(string $message, array $data): void
    {
        $this->log('INFO', $message, $data);
    }

    public function warning(string $message, array $data): void
    {
        $this->log('WARNING', $message, $data);
    }

    public function error(string $message, array $data): void
    {
        $this->log('ERROR', $message, $data);
    }

    private function log(string $level, string $message, array $data): void
    {
        echo sprintf("[%s] %s - %s", $level, $message, json_encode($data, JSON_PRETTY_PRINT)) . PHP_EOL;
    }
}
