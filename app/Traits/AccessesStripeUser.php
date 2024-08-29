<?php

namespace App\Traits;

use App\Models\User;

trait AccessesStripeUser
{
    protected function getUserByStripeId($stripeId): ?User
    {
        if ($user = User::where('stripe_id', $stripeId)->first()) {
            return $user;
        } else {
            return null;
        }
    }
}
