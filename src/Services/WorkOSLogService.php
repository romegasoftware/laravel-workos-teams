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
     * Log an exception
     */
    public function exception(\Exception $e, array $context = []): void
    {
        $this->error($e->getMessage(), array_merge($context, [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]));
    }
}
