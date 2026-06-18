<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Helpers\ActivityLogger;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $conversations = Conversation::where(function ($q) use ($user) {
            $q->whereDoesntHave('participants')
              ->orWhereHas('participants', fn($p) => $p->where('user_id', $user->id))
              ->orWhere('created_by', $user->id);
        })->with('creator', 'messages')->latest()->paginate(15);

        return view('conversations.index', compact('conversations'));
    }

    public function create()
    {
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Parents cannot create new conversations.');
        }

        return redirect()->route('conversations.index');
    }

    public function store(Request $request)
    {
        if (Auth::user()->hasRole('Parent')) {
            abort(403, 'Parents cannot create new conversations.');
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'is_group' => ['nullable', 'boolean'],
        ]);

        $conversation = Conversation::create([
            'subject' => $data['subject'],
            'created_by' => Auth::id(),
            'is_group' => $data['is_group'] ?? false,
        ]);

        $conversation->participants()->attach(Auth::id());

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $data['message'],
        ]);

        event(new MessageSent($message));

        ActivityLogger::log('conversation-created', 'Conversation', $conversation->id, "Created conversation: {$conversation->subject}");

        return redirect()->route('conversations.show', $conversation)->with('success', 'Conversation created.');
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        $isParticipant = $conversation->participants()->where('user_id', $user->id)->exists();
        if (!$isParticipant && $conversation->created_by !== $user->id) {
            if ($conversation->participants()->exists()) {
                abort(403);
            }
            $conversation->participants()->attach($user->id);
        }

        $conversation->load(['messages.sender', 'creator', 'participants']);

        return view('conversations.show', compact('conversation'));
    }

    public function message(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        $isParticipant = $conversation->participants()->where('user_id', $user->id)->exists();
        if (!$isParticipant && $conversation->created_by !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $data['message'],
        ]);

        event(new MessageSent($message));

        $conversation->participants()->syncWithoutDetaching(Auth::id());

        return redirect()->route('conversations.show', $conversation)->with('success', 'Message sent.');
    }

    public function destroy(Conversation $conversation)
    {
        if ($conversation->created_by !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        ActivityLogger::log('conversation-deleted', 'Conversation', $conversation->id, "Deleted conversation: {$conversation->subject}");
        $conversation->delete();

        return redirect()->route('conversations.index')->with('success', 'Conversation deleted.');
    }
}
