<?php

namespace App\Support\AdminOs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Writes to the append-only os_audit_logs table.
 * Never pass health values, card data or full phone numbers in $context.
 */
class Audit
{
    public static function log(string $action, ?Model $subject = null, array $context = [], ?int $actorId = null): void
    {
        try {
            $request = request();

            DB::table('os_audit_logs')->insert([
                'actor_id'     => $actorId ?? auth()->id(),
                'action'       => $action,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id'   => $subject?->getKey() !== null ? (string) $subject->getKey() : null,
                'context'      => $context ? json_encode($context) : null,
                'ip'           => $request?->ip(),
                'user_agent'   => $request ? substr((string) $request->userAgent(), 0, 255) : null,
                'at'           => now(),
            ]);
        } catch (\Throwable $e) {
            // Auditing must never break the request, but a failure must be visible.
            Log::error('[AdminOS] audit write failed', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }
}
