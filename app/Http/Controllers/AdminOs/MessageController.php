<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Services\Messaging\MessageService;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;

/**
 * Family messages (backend spec 12): one inbox for care-team, coordinator
 * and support threads. Staff reply as "CavalCare" and join the thread.
 */
class MessageController extends Controller
{
    public function index(Request $request)
    {
        $kind = $request->query('kind');
        $threads = Thread::with(['patient', 'participants.user'])
            ->when(in_array($kind, ['care_team', 'coordinator', 'support'], true), fn ($q) => $q->where('kind', $kind))
            ->orderByDesc('last_message_at')
            ->paginate(25)->withQueryString();

        // The last message per thread (25 per page); "awaiting reply" when a family member or caregiver sent it.
        $last = $threads->getCollection()->mapWithKeys(fn (Thread $t) => [
            $t->id => Message::where('thread_id', $t->id)->latest('sent_at')->first(),
        ]);

        return view('adminos.messages.index', compact('threads', 'kind', 'last'));
    }

    public function show(Thread $thread)
    {
        $thread->load(['patient', 'participants.user']);
        $messages = $thread->messages()->with('sender')->latest('sent_at')->limit(200)->get()->reverse();
        Audit::log('thread.viewed', $thread);

        return view('adminos.messages.show', compact('thread', 'messages'));
    }

    public function reply(Request $request, Thread $thread, MessageService $messages)
    {
        $data = $request->validate(['text' => ['required', 'string', 'max:4000']]);
        $kind = $request->user()->hasRole('support') ? 'support' : 'coordinator';

        $thread->participants()->firstOrCreate(['user_id' => $request->user()->id], ['role_in_thread' => $kind]);
        $messages->post($thread, $request->user(), $kind, $data['text']);
        Audit::log('thread.replied', $thread);

        return redirect()->route('os.messages.show', $thread)->with('os_flash', ['stable', 'Sent as CavalCare.']);
    }
}
