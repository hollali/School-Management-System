<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Foundation\Events\Dispatchable;

class SubmissionRejected
{
    use Dispatchable;

    public Submission $submission;
    public string $reason;

    public function __construct(Submission $submission, string $reason)
    {
        $this->submission = $submission;
        $this->reason = $reason;
    }
}
