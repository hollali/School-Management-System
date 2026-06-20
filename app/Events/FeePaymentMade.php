<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;

class FeePaymentMade
{
    use Dispatchable;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
