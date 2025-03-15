<?php

namespace RomegaSoftware\WorkOSTeams\Services;

use Illuminate\Support\Facades\Log;

class WorkOSLogService
{
    /**
     * Log an error
     */
    public function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * Log an info message
     */
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    /**
     * Log a warning
     */
    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    /**
     * Log an exception
     */
    public function exception(\Exception $e, array $context = []): void
    {
        $this->error($e->getMessage(), array_merge($context, [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]));
    }

    /**
     * Log a failed operation
     */
    public function failedOperation(string $operation, array $context = []): void
    {
        $this->error("Failed to {$operation}", $context);
    }

    /**
     * Log a successful operation
     */
    public function successfulOperation(string $operation, array $context = []): void
    {
        $this->info("Successfully {$operation}", $context);
    }
}
