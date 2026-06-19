<?php

namespace App\Http\Controllers;

use App\Events\MessageReacted;
use App\Helpers\ActivityLogger;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;

class MessageReactionController extends Controller
{
    public function store(Request $request, Message $message)
    {
        $this->authorize('view', $message->conversation);

        $request->validate([
            'reaction' => 'required|string|max:50',
        ]);

        $user = $request->user();

        $existing = $message->reactions()
            ->where('user_id', $user->id)
            ->where('reaction', $request->reaction)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'action' => 'removed',
                'message_id' => $message->id,
                'reaction' => $request->reaction,
                'user_id' => $user->id,
            ]);
        }

        $reaction = $message->reactions()->create([
            'user_id' => $user->id,
            'reaction' => $request->reaction,
        ]);

        broadcast(new MessageReacted($reaction))->toOthers();

        ActivityLogger::log(
            'reacted to message',
            'message',
            $message->id,
            "{$user->name} reacted with {$request->reaction}"
        );

        return response()->json([
            'action' => 'added',
            'message_id' => $message->id,
            'reaction' => $request->reaction,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);
    }

    public function destroy(Request $request, Message $message, MessageReaction $reaction)
    {
        if ($reaction->user_id !== $request->user()->id) {
            abort(403);
        }

        $reaction->delete();

        return response()->json([
            'action' => 'removed',
            'message_id' => $message->id,
            'reaction' => $reaction->reaction,
            'user_id' => $request->user()->id,
        ]);
    }
}
