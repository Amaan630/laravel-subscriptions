<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class ApiBillingController extends Controller
{
    /**
     * @throws Exception
     */
    public function blackFridayUrl(Request $request)
    {
        $user = User::find($request->get('user_id'));

        return $user->allowPromotionCodes()
            ->newSubscription('default', config('stripe.monthly_price_tier_one'))
            ->checkout([
                'success_url' => route('billing.portal'),
                'cancel_url' => route('billing.portal'),
                'customer_update' => ['address' => 'auto'],
                'automatic_tax' => [
                    'enabled' => true,
                ],
                'discounts' => [
                    ['coupon' => 'k28CxycX'], // this coupon expires on Dec 31 2023
                ],
            ])->url;
    }
}
