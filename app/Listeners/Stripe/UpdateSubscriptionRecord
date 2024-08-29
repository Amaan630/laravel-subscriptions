<?php

namespace App\Listeners\Stripe;

use App\Managers\SubscriptionManager;
use App\Traits\AccessesStripeUser;
use Laravel\Cashier\Events\WebhookReceived;

class UpdateSubscriptionRecord
{
    use AccessesStripeUser;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $object = $payload['data']['object'];
        $subscriptionManager = new SubscriptionManager($object);

        switch ($payload['type']) {
            case 'customer.subscription.created':
                $subscriptionManager->create();
                break;
            case 'customer.subscription.updated':
                $subscriptionManager->update();
                break;
            case 'customer.subscription.deleted':
                $subscriptionManager->cancel();
                break;
            case 'customer.subscription.paused':
                // Not supported in the UI.
                $subscriptionManager->pause();
                break;
            case 'customer.subscription.resumed':
                // Not supported in the UI.
                $subscriptionManager->resume();
                break;
        }
    }
}
