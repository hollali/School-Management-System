<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Foundation\Events\Dispatchable;

class SubmissionSubmitted
{
    use Dispatchable;

    public Submission $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }
}
