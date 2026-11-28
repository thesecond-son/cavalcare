<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\Notify\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Push registration, the in-app inbox and notification settings (spec 7.1). */
class DeviceController extends Controller
{
    /** POST /devices { fcm_token, platform, app_version }: call after sign-in and whenever FCM gives a new token. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fcm_token'   => ['required', 'string', 'max:4096'],
            'platform'    => ['required', Rule::in(['ios', 'android'])],
            'app_version' => ['nullable', 'string', 'max:20'],
        ]);
        $app = $request->user()->currentAccessToken()?->can('app:cavalpro') ? 'cavalpro' : 'cavalcare';
        $hash = hash('sha256', $data['fcm_token']);

        // A token belongs to one install: if another account used this phone, it moves here.
        $device = Device::where('fcm_token_hash', $hash)->first() ?? new Device(['fcm_token_hash' => $hash]);
        $device->fill([
            'user_id'        => $request->user()->id,
            'app'            => $app,
            'platform'       => $data['platform'],
            'fcm_token'      => $data['fcm_token'],
            'app_version'    => $data['app_version'] ?? null,
            'last_active_at' => now(),
        ])->save();

        return response()->json(['data' => ['id' => $device->id, 'app' => $app, 'platform' => $device->platform]], 201);
    }

    /** DELETE /devices/{id}: stop pushes to this phone (call on sign-out). */
    public function destroy(Request $request, string $id)
    {
        Device::where('id', $id)->where('user_id', $request->user()->id)->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    /** GET /notifications?unread=1: the in-app inbox for this app. */
    public function inbox(Request $request)
    {
        $app = $request->user()->currentAccessToken()?->can('app:cavalpro') ? 'cavalpro' : 'cavalcare';
        $rows = DB::table('notifications')->where('notifiable_type', \App\Models\User::class)->where('notifiable_id', $request->user()->id)
            ->when($request->boolean('unread'), fn ($q) => $q->whereNull('read_at'))
            ->orderByDesc('created_at')->limit(100)->get()
            ->map(fn ($n) => ['id' => $n->id, 'event' => $n->type, 'read' => $n->read_at !== null, 'created_at' => \Illuminate\Support\Carbon::parse($n->created_at)->toIso8601String()] + json_decode($n->data, true))
            ->filter(fn ($n) => ($n['app'] ?? $app) === $app)->values();

        return response()->json(['data' => $rows, 'meta' => ['unread' => $rows->where('read', false)->count()]]);
    }

    /** POST /notifications/read { ids?: [] }: mark some or all as read. */
    public function markRead(Request $request)
    {
        $ids = $request->validate(['ids' => ['nullable', 'array', 'max:200'], 'ids.*' => ['uuid']])['ids'] ?? null;
        DB::table('notifications')->where('notifiable_type', \App\Models\User::class)->where('notifiable_id', $request->user()->id)
            ->when($ids, fn ($q) => $q->whereIn('id', $ids))->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['data' => ['read' => true]]);
    }

    /** GET /me/notification-preferences */
    public function preferences(Request $request)
    {
        $set = DB::table('notification_preferences')->where('user_id', $request->user()->id)->where('channel', 'push')->pluck('enabled', 'category');

        return response()->json(['data' => collect(Notifier::CATEGORIES)->map(fn ($c) => [
            'category' => $c, 'push' => (bool) ($set[$c] ?? true),
        ])->values()->all() + [], 'meta' => ['note' => 'Urgent alerts (arrival checks, urgent concerns) are always sent.']]);
    }

    /** PUT /me/notification-preferences { "push": { "messages": false, ... } } */
    public function updatePreferences(Request $request)
    {
        $data = $request->validate(['push' => ['required', 'array'], 'push.*' => ['boolean']]);
        foreach ($data['push'] as $category => $enabled) {
            if (! in_array($category, Notifier::CATEGORIES, true)) {
                continue;
            }
            DB::table('notification_preferences')->updateOrInsert(
                ['user_id' => $request->user()->id, 'channel' => 'push', 'category' => $category],
                ['enabled' => (bool) $enabled, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return $this->preferences($request);
    }
}
