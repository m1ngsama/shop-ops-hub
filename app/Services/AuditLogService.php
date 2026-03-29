<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    public function record(
        string $event,
        ?Request $request = null,
        ?User $user = null,
        ?string $actorLabel = null,
        ?Model $subject = null,
        array $meta = []
    ): AuditLog {
        $actor = $user ?? $request?->user();

        return AuditLog::query()->create([
            'user_id' => $actor?->id,
            'actor_label' => $actorLabel ?: ($actor?->name ?? 'public-web'),
            'event' => $event,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'meta' => $meta,
        ]);
    }
}
