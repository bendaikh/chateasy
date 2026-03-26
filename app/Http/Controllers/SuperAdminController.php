<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WhatsappProfile;
use App\Models\Message;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'active_customers' => User::where('role', 'customer')->where('is_active', true)->count(),
            'total_whatsapp_profiles' => WhatsappProfile::count(),
            'active_profiles' => WhatsappProfile::where('status', 'connected')->count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount'),
        ];
        
        $recent_customers = User::where('role', 'customer')
            ->latest()
            ->take(10)
            ->get();
            
        $subscription_breakdown = Subscription::select('plan', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('plan')
            ->get();
        
        return view('superadmin.dashboard', compact('stats', 'recent_customers', 'subscription_breakdown'));
    }
    
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->withCount('whatsappProfiles')
            ->latest()
            ->paginate(20);
            
        return view('superadmin.customers', compact('customers'));
    }
    
    public function analytics()
    {
        return view('superadmin.analytics');
    }
}
