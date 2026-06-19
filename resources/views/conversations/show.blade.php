@php
    header('Location: ' . route('conversations.index', ['conversation' => $conversation->id]));
    exit;
@endphp
