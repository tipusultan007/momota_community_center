<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Http\Request;

class SuperadminController extends Controller
{
    public function index(Request $request)
    {
        $business = BusinessSetting::get();
        $platformRevenue = Income::sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_tenants' => 1,
                    'active_tenants' => 1,
                    'platform_revenue' => $platformRevenue,
                ],
                'recent_tenants' => [
                    [
                        'id' => 1,
                        'name' => $business->company_name,
                        'status' => 'active',
                        'users_count' => User::count(),
                        'created_at' => $business->created_at?->format('d M, Y') ?? date('d M, Y'),
                    ]
                ],
            ],
        ]);
    }

    public function systemLogs(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'action' => 'সিস্টেম স্ট্যাটাস', 'details' => 'স্বাভাবিক এবং সক্রিয়', 'time' => 'এখন'],
            ],
        ]);
    }
}
