<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'plans' => [],
                'gateways' => [],
                'current_subscription' => [
                    'plan' => 'enterprise',
                    'trial_ends_at' => null,
                    'subscription_ends_at' => null,
                    'is_active' => true,
                ]
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'সফটওয়্যারটি স্থায়ীভাবে সক্রিয় রয়েছে।',
        ]);
    }

    public function history()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'data' => [],
                'total' => 0,
            ],
        ]);
    }
}
