<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $conversations = Conversation::with('creator')->latest()->paginate(15);

        return view('conversations.index', compact('conversations'));
    }

    public function create()
    {
        return view('conversations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $conversation = Conversation::create([
            'subject' => $data['subject'],
            'created_by' => Auth::id(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $data['message'],
        ]);

        return redirect()->route('conversations.show', $conversation)->with('success', 'Conversation created.');
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['messages.sender', 'creator']);

        return view('conversations.show', compact('conversation'));
    }

    public function message(Request $request, Conversation $conversation)
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $data['message'],
        ]);

        return redirect()->route('conversations.show', $conversation)->with('success', 'Message sent.');
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('conversations.index')->with('success', 'Conversation deleted.');
    }
}
