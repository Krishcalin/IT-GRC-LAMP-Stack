<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/** Thin wrapper to record an entry in the activity_log. */
class Activity
{
    public static function log(string $action, string $resourceType, ?string $resourceId = null, ?array $details = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
