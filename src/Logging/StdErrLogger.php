<?php
namespace WebmergeOfficeTools\Logging;

class StdErrLogger implements LoggingInterface
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
        $content = [
            'message' => $message,
            'level' => $level,
            'context' => $data,
        ];

        fwrite(STDERR, json_encode($content) . PHP_EOL);
    }
}
