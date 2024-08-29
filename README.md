# laravel-subscriptions
Add fully-working subscriptions to your laravel project in minutes.

> **side note**: the commit history is awful - i copied everything over directly using the github ui

## post-installation steps:

1. Add the proper routes to your `web.php`
```
Route::middleware([])->group(function () {
    Route::get('/billing', BillingController::class)->name('billing.portal');
    Route::get('/billing/checkout', CheckoutController::class)->name('billing.checkout');
});
```

2. Add the event listener for the Stripe webhook to the `EventServiceProvider.php`
```
use App\Listeners\Webhook\UpdateSubscriptionRecord;
use Laravel\Cashier\Events\WebhookReceived;

WebhookReceived::class => [
    UpdateSubscriptionRecord::class,
],
```

3. Update the `User.php` model by adding the new traits and appends
```
use Billable;
use BillableHelper;

protected $appends = [
    'subscribed',
    'on_trial',
];

public function getSubscribedAttribute(): bool
{
    return $this->subscribed();
}

public function getOnTrialAttribute(): bool
{
    // if is on trial
    if ($this->trial_ends_at && now()->lt($this->trial_ends_at)) {
        return true;
    }
    return false;
}
```

4. Add the stripe `.env` keys
```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK=
STRIPE_PRICE=
```
