<?php

namespace App\Traits;

use App\Models\User;
use function Illuminate\Events\queueable;

trait BillableHelper
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updated(queueable(function (User $customer) {
            if ($customer->hasStripeId()) {
                $customer->syncStripeCustomerDetails();
            }
        }));
    }
}
