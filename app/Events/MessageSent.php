<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent
{
    use Dispatchable;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }
}
