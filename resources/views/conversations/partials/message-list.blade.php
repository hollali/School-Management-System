@foreach($messages as $message)
    @include('conversations.partials.message', ['message' => $message])
@endforeach
