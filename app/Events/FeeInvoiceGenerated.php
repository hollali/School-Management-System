<?php

namespace App\Events;

use App\Models\Fee;
use Illuminate\Foundation\Events\Dispatchable;

class FeeInvoiceGenerated
{
    use Dispatchable;

    public Fee $fee;

    public function __construct(Fee $fee)
    {
        $this->fee = $fee;
    }
}
