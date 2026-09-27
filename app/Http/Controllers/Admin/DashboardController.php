<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\SubscriptionHistory;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $pendingSubscriptions = SubscriptionHistory::where('status', 'pending')->count();
        $totalRevenue = SubscriptionHistory::where('status', 'active')->sum('amount');
        
        $recentSubscriptions = SubscriptionHistory::with('tenant')
            ->latest()
            ->take(5)
            ->get();

        $activeGateways = PaymentGateway::where('is_active', true)->count();

        return view('admin.dashboard', compact(
            'totalTenants', 
            'activeTenants', 
            'pendingSubscriptions', 
            'totalRevenue',
            'recentSubscriptions',
            'activeGateways'
        ));
    }
}
