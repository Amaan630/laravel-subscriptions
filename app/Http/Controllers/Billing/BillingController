<?php

namespace App\Http\Controllers\Billing;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Subscription;

class BillingController
{
    /**
     * @throws Exception
     */
    public function __invoke(): Response|RedirectResponse
    {
        $user = Auth::user();

        $billing_portal_url = '';
        $next_billing_date = null;

        if ($user->subscribed()) {
            $billing_portal_url = $user->billingPortalUrl(route('job.index'));

            if ($user->subscription()->active()) {
                $created_at = $user->subscription()->created_at;

                // now we can calculate the next billing date
                // add on the appropriate number of months.
                // basically add on the number of months old the subscription is + 1
                $next_billing_date = $created_at->addMonths($created_at->diffInMonths(Carbon::now()) + 1);
            }
        }


        return Inertia::render('Billing/Portal', [
            'user_count' => Subscription::whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->count(),
            'billing_portal_url' => $billing_portal_url,
            'next_billing_date' => $next_billing_date,
        ]);
    }
}
