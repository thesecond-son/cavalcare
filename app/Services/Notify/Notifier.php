<?php

namespace App\Services\Notify;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One place to tell someone something (backend spec 9).
 * Every notice goes into the user's in-app inbox; a push goes to their
 * phones for that app unless they muted the category. Urgent notices
 * (arrival checks, urgent concerns) ignore mutes (spec 9.3).
 */
class Notifier
{
    public const CATEGORIES = ['visits', 'reports', 'messages', 'bookings', 'shifts'];

    public function __construct(private PushSender $push)
    {
    }

    /**
     * @param string $app      cavalcare | cavalpro: which app's phones get the push
     * @param array  $data     ids only, e.g. ['visit_id' => '...']; no health data
     */
    public function send(User $user, string $app, string $category, string $event, string $title, string $body, array $data = [], bool $urgent = false): void
    {
        DB::table('notifications')->insert([
            'id'              => (string) Str::uuid(),
            'type'            => $event,
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'data'            => json_encode(['app' => $app, 'category' => $category, 'title' => $title, 'body' => $body, 'urgent' => $urgent] + $data),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        if (! $urgent && $this->muted($user, $category)) {
            return;
        }

        Device::where('user_id', $user->id)->where('app', $app)->get()
            ->each(function (Device $device) use ($title, $body, $data, $event, $urgent) {
                try {
                    $this->push->send($device, $title, $body, $data + ['event' => $event], $urgent);
                } catch (\Throwable $e) {
                    report($e);   // a push failure never breaks the action that caused it
                }
            });
    }

    /** Sends to several users at once. */
    public function sendMany(iterable $users, string $app, string $category, string $event, string $title, string $body, array $data = [], bool $urgent = false): void
    {
        foreach ($users as $user) {
            $this->send($user, $app, $category, $event, $title, $body, $data, $urgent);
        }
    }

    private function muted(User $user, string $category): bool
    {
        return DB::table('notification_preferences')
            ->where('user_id', $user->id)->where('channel', 'push')->where('category', $category)
            ->where('enabled', false)->exists();
    }
}
