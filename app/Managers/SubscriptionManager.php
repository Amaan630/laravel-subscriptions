<?php

namespace App\Managers;

use App\Events\SubscriptionCanceled;
use App\Events\SubscriptionCreated;
use App\Events\SubscriptionUpdated;
use App\Models\User;
use App\Traits\AccessesStripeUser;
use Carbon\Carbon;
use Stripe\Subscription;

class SubscriptionManager
{
    use AccessesStripeUser;

    private object|array $object;

    private ?User $user;

    /**
     * Create the manager.
     *
     * @return void
     */
    public function __construct(object|array $object)
    {
        $this->object = $object;
        $this->user = $this->getUserByStripeId($object['customer']);
    }

    /**
     * Creates a corresponding subscription record for the
     * webhook object, and returns true upon completion.
     */
    public function create(): bool
    {
        if (! $this->user) {
            return false;
        }

        $this->user->subscriptions()->create([
            'name' => 'default',
            'stripe_id' => $this->object['id'],
            'stripe_status' => $this->object['status'],
            'stripe_price' => $this->object['items']['data'][0]['plan']['id'],
            'quantity' => 1,
        ]);
        event(new SubscriptionCreated($this->user));

        return true;
    }

    /**
     * Updates the corresponding subscription record for the
     * webhook object, and returns true upon completion.
     */
    public function update(): bool
    {
        if (! $this->user) {
            return false;
        }
        if (! $this->user->subscriptions()->where('stripe_id', $this->object['id'])->exists()) {
            return false;
        }

        $subscription = $this->user->subscriptions()->where('stripe_id', $this->object['id'])->first();
        $subscription->stripe_status = $this->object['status'];
        $subscription->stripe_price = $this->object['items']['data'][0]['price']['id'];

        if (isset($this->object['cancel_at_period_end'])) {
            if ($this->object['cancel_at_period_end']) {
                $subscription->ends_at = Carbon::createFromTimestamp(
                    $this->object['current_period_end']
                );
                event(new SubscriptionCanceled($this->user));
            } elseif (isset($this->object['cancel_at'])) {
                $subscription->ends_at = Carbon::createFromTimestamp(
                    $this->object['cancel_at']
                );
                event(new SubscriptionCanceled($this->user));
            } else {
                $subscription->ends_at = null;
                event(new SubscriptionUpdated($this->user));
            }
        }

        $subscription->save();

        return true;
    }

    /**
     * Immediately cancels the corresponding subscription record for the
     * webhook object, and returns true upon completion.
     */
    public function cancel(): bool
    {
        if (! $this->user) {
            return false;
        }

        $subscription = $this->user->subscriptions()->where('stripe_id', $this->object['id'])->first();
        $subscription->stripe_status = Subscription::STATUS_CANCELED;

        if ($this->object['cancel_at_period_end']) {
            $subscription->ends_at = Carbon::now();
        } else {
            $subscription->ends_at = $this->object['canceled_at'];
        }
        $subscription->save();
        event(new SubscriptionCanceled($this->user));

        return true;
    }

    /**
     * Pauses the corresponding subscription record for the
     * webhook object, and returns true upon completion.
     */
    public function pause(): bool
    {
        if (! $this->user) {
            return false;
        }

        $subscription = $this->user->subscriptions()->where('stripe_id', $this->object['id'])->first();
        $subscription->stripe_status = Subscription::STATUS_PAUSED;
        $subscription->save();

        return true;
    }

    /**
     * Resume the corresponding subscription record for the
     * webhook object, and returns true upon completion.
     */
    public function resume(): bool
    {
        if (! $this->user) {
            return false;
        }

        $subscription = $this->user->subscriptions()->where('stripe_id', $this->object['id'])->first();
        $subscription->stripe_status = Subscription::STATUS_ACTIVE;
        $subscription->ends_at = null;
        $subscription->save();

        return true;
    }
}
