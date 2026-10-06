<?php

declare(strict_types=1);

use App\Models\StaffProfileModel;

if (! function_exists('staff_campus_id')) {
    function staff_campus_id(): ?int
    {
        $user = auth()->user();
        if ($user === null || $user->inGroup('admin', 'ict')) return null;
        $campusId = (new StaffProfileModel())->campusIdForUser((int) $user->id);
        return $campusId ?? -1;
    }
}

if (! function_exists('staff_can')) {
    function staff_can(string ...$groups): bool
    {
        $user = auth()->user();
        return $user !== null && $user->inGroup(...$groups);
    }
}
