<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Care\Patient;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\Messaging\ThreadParticipant;
use App\Models\Upload;
use App\Services\Messaging\MessageService;
use App\Support\ApiError;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Messages for both apps (backend spec 7.8, 8.7). A person only ever sees
 * threads they take part in. Family Viewers read but don't send.
 */
class ThreadController extends Controller
{
    public function __construct(private MessageService $messages)
    {
    }

    /** GET /threads: my threads, newest activity first, with unread counts. */
    public function index(Request $request)
    {
        $me = $request->user();
        $parts = ThreadParticipant::with('thread')->where('user_id', $me->id)->get()->filter(fn ($p) => $p->thread);

        $data = $parts->map(function ($p) use ($me) {
            $t = $p->thread;
            $last = $t->messages()->latest('sent_at')->first();

            return [
                'id'           => $t->id,
                'kind'         => $t->kind,
                'title'        => $t->title,
                'patient_id'   => $t->patient_id,
                'read_only'    => $p->read_only,
                'muted'        => $p->muted,
                'unread'       => Message::where('thread_id', $t->id)->where('sent_at', '>', $p->last_read_at ?? '1970-01-01')
                                    ->where(fn ($q) => $q->whereNull('sender_id')->orWhere('sender_id', '!=', $me->id))->count(),
                'last_message' => $last ? ['text' => $last->text, 'sender_kind' => $last->sender_kind, 'sent_at' => $last->sent_at->toIso8601String()] : null,
            ];
        })->sortByDesc(fn ($t) => $t['last_message']['sent_at'] ?? '')->values();

        return response()->json(['data' => $data]);
    }

    /** POST /threads: start a conversation with CavalCare (kind coordinator or support). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'kind'       => ['required', Rule::in(['coordinator', 'support'])],
            'patient_id' => ['nullable', 'uuid'],
            'text'       => ['required', 'string', 'max:4000'],
            'client_id'  => ['nullable', 'uuid'],
        ]);
        $me = $request->user();

        if (! empty($data['patient_id'])) {
            $patient = Patient::with('members')->find($data['patient_id']);
            abort_unless($patient && $patient->members->contains('user_id', $me->id), 404);
        }

        $role = $me->professional ? 'professional' : 'family';
        $thread = Thread::create([
            'kind'       => $data['kind'],
            'patient_id' => $data['patient_id'] ?? null,
            'title'      => ($data['kind'] === 'support' ? 'Support' : 'CavalCare team') . ' · ' . ($me->name ?: 'Family'),
        ]);
        $thread->participants()->create(['user_id' => $me->id, 'role_in_thread' => $role]);
        $this->messages->post($thread, $me, $role, $data['text'], $data['client_id'] ?? null);

        return response()->json(['data' => ['id' => $thread->id, 'kind' => $thread->kind, 'title' => $thread->title]], 201);
    }

    /** GET /threads/{id}/messages?before=ISO8601 (50 per page, oldest first within the page). */
    public function messages(Request $request, string $id)
    {
        $part = $this->participant($request, $id);
        $before = $request->query('before');

        $page = Message::with('sender')->where('thread_id', $id)
            ->when($before, fn ($q) => $q->where('sent_at', '<', \Illuminate\Support\Carbon::parse($before)))
            ->orderByDesc('sent_at')->limit(50)->get()->reverse()->values();

        return response()->json([
            'data' => $page->map(fn (Message $m) => [
                'id'           => $m->id,
                'client_id'    => $m->client_id,
                'sender_kind'  => $m->sender_kind,
                'sender_name'  => in_array($m->sender_kind, ['coordinator', 'support'], true) ? 'CavalCare' : $m->sender?->name,
                'is_mine'      => $m->sender_id === $request->user()->id,
                'text'         => $m->text,
                'has_attachment' => (bool) $m->attachment_path,
                'system_event' => $m->system_event,
                'sent_at'      => $m->sent_at->toIso8601String(),
            ]),
            'meta' => ['older_before' => $page->first()?->sent_at?->toIso8601String(), 'read_only' => $part->read_only],
        ]);
    }

    /** POST /threads/{id}/messages { client_id, text, upload_id? } */
    public function send(Request $request, string $id)
    {
        $part = $this->participant($request, $id);
        if ($part->read_only) {
            return ApiError::make('role_not_allowed', 'You can read this conversation but not send to it.', 403);
        }
        $data = $request->validate([
            'client_id' => ['required', 'uuid'],
            'text'      => ['required_without:upload_id', 'nullable', 'string', 'max:4000'],
            'upload_id' => ['nullable', 'uuid'],
        ]);

        $path = null;
        if (! empty($data['upload_id'])) {
            $upload = Upload::where('id', $data['upload_id'])->where('user_id', $request->user()->id)->whereNull('attached_at')->first();
            if (! $upload) {
                return ApiError::make('validation_failed', 'Upload the photo first.', 422, ['upload_id' => ['That upload was not found.']]);
            }
            $upload->update(['attached_at' => now()]);
            $path = $upload->path;
        }

        $m = $this->messages->post($part->thread, $request->user(), $part->role_in_thread, $data['text'] ?? null, $data['client_id'], $path);

        return response()->json(['data' => ['id' => $m->id, 'client_id' => $m->client_id, 'sent_at' => $m->sent_at->toIso8601String()]], 201);
    }

    /** POST /threads/{id}/read: mark everything up to now as read. */
    public function read(Request $request, string $id)
    {
        $this->participant($request, $id)->update(['last_read_at' => now()]);

        return response()->json(['data' => ['read' => true]]);
    }

    private function participant(Request $request, string $threadId): ThreadParticipant
    {
        $p = ThreadParticipant::with('thread')->where('thread_id', $threadId)->where('user_id', $request->user()->id)->first();
        abort_unless($p, 404);

        return $p;
    }
}
