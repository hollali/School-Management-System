<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Foundation\Events\Dispatchable;

class SubmissionRetracted
{
    use Dispatchable;

    public Submission $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }
}
