<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Log;

/**
 * Structured activity logging utility with data masking.
 *
 * Provides centralized logging for application events with automatic
 * sensitive data masking and contextual information (request ID, IP, etc).
 * All logs are JSON-structured for easy parsing by log aggregation systems.
 */
class LogActivity
{
    /**
     * Log an informational event.
     *
     * @param string $action Action/event identifier
     * @param array $context Additional contextual data (auto-masked for sensitive fields)
     */
    public static function info(string $action, array $context = []): void
    {
        self::log('info', $action, $context);
    }

    public static function warning(string $action, array $context = []): void
    {
        self::log('warning', $action, $context);
    }

    public static function error(string $action, array $context = []): void
    {
        self::log('error', $action, $context);
    }

    public static function critical(string $action, array $context = []): void
    {
        self::log('critical', $action, $context);
    }

    private static function log(string $level, string $action, array $context): void
    {
        // Mask sensitive data sebelum logging
        $maskedContext = DataMasking::mask($context);

        $data = array_merge([
            'action'      => $action,
            'request_id'  => request()->attributes->get('request_id'),
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'timestamp'   => now()->toIso8601String(),
        ], $maskedContext);

        Log::{$level}("[{$action}]", $data);
    }
}
