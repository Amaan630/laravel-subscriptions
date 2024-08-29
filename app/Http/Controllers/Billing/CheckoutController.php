<?php

namespace App\Http\Controllers\Billing;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckoutController
{
    /**
     * Redirects the user to the Stripe Checkout page.
     *
     * @throws Exception
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $price = config('stripe.monthly_price_tier_one');
//        if ($request->has('price')) {
//            switch ($request->get('price')) {
//                case '12.99':
//                    $price = config('stripe.monthly_price_tier_two');
//                    break;
//                case '129.99':
//                    $price = config('stripe.annual_price_tier_two');
//                    break;
//                case '14.99':
//                    $price = config('stripe.monthly_price_tier_three');
//                    break;
//                case '149.99':
//                    $price = config('stripe.annual_price_tier_three');
//                    break;
//            }
//        }

        $user = Auth::user();

        $url = $user->allowPromotionCodes()
            ->newSubscription('default', $price)
            ->checkout([
                'success_url' => route('billing.portal'),
                'cancel_url' => route('billing.portal'),
                'customer_update' => ['address' => 'auto'],
                'automatic_tax' => [
                    'enabled' => true,
                ],
                //                'discounts' => [
                //                    ['coupon' => 'k28CxycX'], // this coupon expires on Dec 31 2023
                //                ],
            ])->url;

        // Using Inertia::location instead of redirect()
        // avoided a CORS issue.
        return Inertia::location($url);
    }
}
