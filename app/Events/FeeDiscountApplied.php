<?php

namespace App\Events;

use App\Models\Discount;
use App\Models\Fee;
use Illuminate\Foundation\Events\Dispatchable;

class FeeDiscountApplied
{
    use Dispatchable;

    public Fee $fee;
    public Discount $discount;

    public function __construct(Fee $fee, Discount $discount)
    {
        $this->fee = $fee;
        $this->discount = $discount;
    }
}
