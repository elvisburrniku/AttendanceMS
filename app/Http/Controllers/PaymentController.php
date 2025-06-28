<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe secret key from environment
        if (env('STRIPE_SECRET_KEY')) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        }
    }

    /**
     * Handle trial signup
     */
    public function trialSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create trial user account
            $user = User::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(12)), // Random password for trial
                'email_verified_at' => now(),
                'trial_ends_at' => now()->addDays(14),
                'role' => 'admin'
            ]);

            // Auto-login the user
            auth()->login($user);

            return redirect()->route('admin')->with('success', 'Welcome! Your 14-day free trial has started. You can explore all features without any limitations.');

        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong during signup. Please try again.');
        }
    }

    /**
     * Create Stripe checkout session
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            // Check if Stripe is configured
            if (!env('STRIPE_SECRET_KEY')) {
                return back()->with('error', 'Payment processing is not configured. Please contact support.');
            }

            $domain = request()->getSchemeAndHttpHost();
            
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'AttendanceTracker Pro - Monthly Subscription',
                                'description' => 'Complete attendance management system with NFC support, GPS tracking, and advanced reporting.',
                            ],
                            'unit_amount' => 2900, // $29.00 in cents
                            'recurring' => [
                                'interval' => 'month',
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => $domain . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $domain . '/cancel',
                'automatic_tax' => [
                    'enabled' => true,
                ],
                'customer_email' => auth()->user()->email ?? null,
            ]);

            return redirect($checkout_session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Unable to create payment session. Please try again or contact support.');
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        try {
            if (env('STRIPE_SECRET_KEY') && $sessionId) {
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                
                // Update user subscription status
                if (auth()->check()) {
                    $user = auth()->user();
                    $user->update([
                        'stripe_customer_id' => $session->customer,
                        'subscription_status' => 'active',
                        'subscription_ends_at' => now()->addMonth(),
                    ]);
                }
            }

            return view('payment.success');

        } catch (\Exception $e) {
            return view('payment.success')->with('warning', 'Payment completed but there was an issue updating your account. Please contact support.');
        }
    }

    /**
     * Handle cancelled payment
     */
    public function cancel()
    {
        return view('payment.cancel');
    }
}