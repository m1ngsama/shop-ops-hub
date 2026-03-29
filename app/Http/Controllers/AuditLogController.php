<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $event = $request->string('event')->toString();
        $actor = trim((string) $request->string('actor'));

        return view('audit.index', [
            'logs' => AuditLog::query()
                ->with('user')
                ->when($event !== '', fn ($query) => $query->where('event', $event))
                ->when($actor !== '', function ($query) use ($actor) {
                    $query->where(function ($query) use ($actor) {
                        $query
                            ->where('actor_label', 'like', "%{$actor}%")
                            ->orWhereHas('user', fn ($userQuery) => $userQuery
                                ->where('name', 'like', "%{$actor}%")
                                ->orWhere('email', 'like', "%{$actor}%"));
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'events' => AuditLog::query()
                ->select('event')
                ->distinct()
                ->orderBy('event')
                ->pluck('event'),
            'filters' => [
                'event' => $event,
                'actor' => $actor,
            ],
        ]);
    }
}
