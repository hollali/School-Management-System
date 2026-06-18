<?php

namespace App\Events;

use App\Models\AssignmentFeedback;
use Illuminate\Foundation\Events\Dispatchable;

class AssignmentGraded
{
    use Dispatchable;

    public AssignmentFeedback $feedback;

    public function __construct(AssignmentFeedback $feedback)
    {
        $this->feedback = $feedback;
    }
}
