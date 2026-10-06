<?php

declare(strict_types=1);

if (! function_exists('format_manila_date')) {
    function format_manila_date(?string $value): string
    {
        return format_manila_time($value, 'F j, Y');
    }
}

if (! function_exists('format_manila_datetime')) {
    function format_manila_datetime(?string $value): string
    {
        return format_manila_time($value, 'F j, Y g:i A');
    }
}

if (! function_exists('format_manila_time')) {
    function format_manila_time(?string $value, string $format): string
    {
        if ($value === null || trim($value) === '') {
            return 'Not available';
        }

        try {
            return (new \DateTimeImmutable($value, new \DateTimeZone('Asia/Manila')))->format($format);
        } catch (\Throwable) {
            return 'Not available';
        }
    }
}
