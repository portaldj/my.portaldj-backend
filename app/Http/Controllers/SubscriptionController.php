<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Subscription;
use App\Services\FlowService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    protected $flowService;

    // Plans Configuration
    const PLANS = [
        'monthly' => [
            'id' => 'monthly',
            'name' => 'Monthly PRO',
            'amount' => 5990,
            'interval' => 'month'
        ],
        'yearly' => [
            'id' => 'yearly',
            'name' => 'Yearly PRO',
            'amount' => 59990,
            'interval' => 'year',
            'savings_pct' => 17 // (5990*12 - 59990) / (5990*12) approx
        ]
    ];

    public function __construct(FlowService $flowService)
    {
        $this->flowService = $flowService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Subscription/Index', [
            'plans' => self::PLANS,
            'isPro' => $request->user()->is_pro,
            'activeSubscription' => $request->user()->subscriptions()->active()->latest()->first(),
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|in:monthly,yearly',
        ]);

        $plan = self::PLANS[$request->plan_id];
        $user = $request->user();
        $orderId = 'SUB-' . $user->id . '-' . time();

        // 1. Create Pending Subscription in DB
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'flow_order' => $orderId,
            'plan_id' => $plan['id'],
            'amount' => $plan['amount'],
            'status' => 'pending',
        ]);

        // 2. Prepare Flow Data
        $flowData = [
            'commerceOrder' => $orderId,
            'subject' => 'Subscription Portal DJ - ' . $plan['name'],
            'currency' => 'CLP',
            'amount' => $plan['amount'],
            'email' => $user->email,
            // 'paymentMethod' => 9, // Let user choose on Flow
            'urlConfirmation' => route('subscription.confirm'), // Webhook
            'urlReturn' => route('subscription.return'), // User Redirect
        ];

        // 3. Request Payment URL from Flow
        try {
            $response = $this->flowService->createPayment($flowData);

            // Flow returns 'url' and 'token'
            if (isset($response['url']) && isset($response['token'])) {
                // Update with token
                $subscription->update(['token' => $response['token']]);

                // Redirect user to Flow
                return Inertia::location($response['url'] . '?token=' . $response['token']);
            }

            return back()->with('error', 'Could not initiate payment with Flow.');

        } catch (\Exception $e) {
            Log::error('Subscription Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'System error initiating payment.');
        }
    }

    /**
     * User is redirected here from Flow after payment interaction
     */
    public function return(Request $request)
    {
        // Flow sends back via POST usually, but sometimes GET depending on config.
        // Docs verify it's a POST to urlReturn? 
        // Actually Flow usually redirects via POST with 'token' or expects us to query status.
        // Let's assume we get the token via POST or query params.

        $token = $request->input('token');

        if (!$token) {
            return redirect()->route('subscription.index')->with('error', 'Invalid return from payment gateway.');
        }

        try {
            $status = $this->flowService->getStatus($token);

            // status['status']: 1 = Pending, 2 = Paid, 3 = Rejected, 4 = Cancelled
            // We should find the subscription by token or flowOrder
            $subscription = Subscription::where('token', $token)->firstOrFail();

            // Restore Session if lost
            if ($subscription && !auth()->check()) {
                auth()->login($subscription->user);
            }

            if ($status['status'] == 2) {
                // Payment Successful
                if ($subscription->status !== 'paid') {
                    $this->activateSubscription($subscription, $status['paymentData']['date'] ?? now());
                }

                return redirect()->route('subscription.success');
            } elseif ($status['status'] == 3 || $status['status'] == 4) {
                $subscription->update(['status' => 'cancelled']);
                return redirect()->route('subscription.index')->with('error', 'Payment was rejected or cancelled.');
            } else {
                return redirect()->route('subscription.index')->with('message', 'Payment is pending processing.');
            }

        } catch (\Exception $e) {
            Log::error('Return Error', ['error' => $e->getMessage()]);
            return redirect()->route('subscription.index')->with('error', 'Error verifying payment status.');
        }
    }

    public function activateTrial(Request $request)
    {
        $user = $request->user();

        // Check eligibility (3 months cooldown)
        if ($user->last_trial_at && $user->last_trial_at->gt(now()->subMonths(3))) {
            return redirect()->route('subscription.index')
                ->with('error', 'You are not eligible for a free trial at this time. Trials are available once every 3 months.');
        }

        // Create Trial Subscription
        Subscription::create([
            'user_id' => $user->id,
            'flow_order' => 'TRIAL-' . $user->id . '-' . time(),
            'plan_id' => 'trial',
            'amount' => 0,
            'status' => 'paid',
            'paid_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        // Update User
        $user->update(['last_trial_at' => now()]);

        return redirect()->route('subscription.success')->with('success', '7-Day Free Trial Activated!');
    }

    /**
     * success page
     */
    public function success()
    {
        return Inertia::render('Subscription/Success');
    }

    /**
     * Webhook confirmation (Server to Server)
     */
    public function confirm(Request $request)
    {
        try {
            $token = $request->input('token');
            if (!$token)
                return response('No token', 400);

            $status = $this->flowService->getStatus($token);
            $subscription = Subscription::where('flow_order', $status['flowOrder'])->first();

            if ($subscription && $status['status'] == 2 && $subscription->status !== 'paid') {
                $this->activateSubscription($subscription, $status['paymentData']['date'] ?? now());
            }

            return response('OK');

        } catch (\Exception $e) {
            Log::error('Webhook Error', ['error' => $e->getMessage()]);
            return response('Error', 500);
        }
    }

    protected function activateSubscription(Subscription $subscription, $paymentDate)
    {
        // Determine expiration
        $interval = $subscription->plan_id === 'yearly' ? 'addYear' : 'addMonth';
        $expiresAt = \Carbon\Carbon::parse($paymentDate)->$interval();

        $subscription->update([
            'status' => 'paid',
            'paid_at' => $paymentDate,
            'expires_at' => $expiresAt,
        ]);

        // Notify User (Email) - To Do
    }
}
